from datetime import datetime
import os
from typing import Any, Dict, List, Optional

import requests
from dotenv import load_dotenv
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel

load_dotenv()

GROQ_API_KEY = os.getenv("GROQ_API_KEY", "")
GROQ_URL = "https://api.groq.com/openai/v1/chat/completions"
GROQ_MODEL = os.getenv("GROQ_MODEL", "llama-3.3-70b-versatile")
MAX_CONTEXT_CHARS = int(os.getenv("ASSETS_AI_MAX_CONTEXT_CHARS", "12000"))
MAX_QUESTION_CHARS = int(os.getenv("ASSETS_AI_MAX_QUESTION_CHARS", "600"))


class ChatMessage(BaseModel):
    message: str
    data_source: Optional[str] = "overall"
    data: Optional[Dict[str, Any]] = None
    context: Optional[Dict[str, Any]] = None


class ChatResponse(BaseModel):
    response: str
    timestamp: str
    data_source: str
    chart: Optional[Dict[str, Any]] = None


app = FastAPI(title="Assets AI Insights API", version="0.1.0")
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


def _rows(data: Optional[Dict[str, Any]], key: str) -> List[Dict[str, Any]]:
    if not data or not isinstance(data.get(key), list):
        return []
    return [row for row in data[key] if isinstance(row, dict)]


def _metric(data: Optional[Dict[str, Any]], key: str, fallback: Any = 0) -> Any:
    if not data or not isinstance(data.get("summary"), dict):
        return fallback
    return data["summary"].get(key, fallback)


def _format_table(title: str, rows: List[Dict[str, Any]], label_key: str = "label", value_key: str = "value") -> str:
    if not rows:
        return f"{title}: no data\n"
    lines = [f"{title}:"]
    for idx, row in enumerate(rows[:12], 1):
        lines.append(f"{idx}. {row.get(label_key, 'Unknown')}: {row.get(value_key, 0)}")
    return "\n".join(lines) + "\n"


def build_context(data: Optional[Dict[str, Any]], data_source: str) -> str:
    if not data:
        return "No system data was provided."

    lines = ["ASSETS MANAGEMENT SYSTEM DATA", f"Data source: {data_source}", ""]
    summary = data.get("summary", {}) if isinstance(data.get("summary"), dict) else {}
    if summary:
        lines.append("Summary metrics:")
        for key, value in summary.items():
            lines.append(f"- {key}: {value}")
        lines.append("")

    sections = [
        ("Assets by status", "asset_status_counts"),
        ("Assets by type", "asset_type_counts"),
        ("Assets by location", "asset_location_counts"),
        ("Components by status", "component_status_counts"),
        ("Maintenance by status", "maintenance_status_counts"),
        ("Maintenance monthly trend", "maintenance_monthly_counts"),
        ("Maintenance by type", "maintenance_type_counts"),
        ("Ticket/fault counts", "ticket_fault_counts"),
        ("Disposal status counts", "disposal_status_counts"),
        ("Write-off reason counts", "write_off_reason_counts"),
        ("Top maintenance assets", "top_maintenance_assets"),
    ]
    for title, key in sections:
        rows = _rows(data, key)
        if rows:
            lines.append(_format_table(title, rows).rstrip())
            lines.append("")

    recent = _rows(data, "recent_records")
    if recent:
        lines.append("Recent records:")
        for row in recent[:20]:
            bits = [f"{k}: {v}" for k, v in row.items() if v not in (None, "")]
            lines.append("- " + "; ".join(bits[:8]))

    return "\n".join(lines).strip()


def trim_context(context: str) -> str:
    if len(context) <= MAX_CONTEXT_CHARS:
        return context
    return context[:MAX_CONTEXT_CHARS] + "\n\n[Context trimmed to keep the request safe and fast.]"


def safe_chart(chart: Optional[Dict[str, Any]]) -> Optional[Dict[str, Any]]:
    if not chart:
        return None
    chart_type = chart.get("type") if chart.get("type") in {"bar", "line", "doughnut", "pie", "table"} else "bar"
    out: Dict[str, Any] = {"type": chart_type, "title": str(chart.get("title", "AI Graph"))[:120]}
    if chart_type == "table":
        columns = [str(column)[:80] for column in chart.get("columns", [])[:8]]
        out["columns"] = columns
        out["rows"] = [
            {column: str(row.get(column, ""))[:180] for column in columns}
            for row in chart.get("rows", [])[:25]
            if isinstance(row, dict)
        ]
        return out
    out["labels"] = [str(label)[:80] for label in chart.get("labels", [])[:20]]
    out["datasets"] = []
    for dataset in chart.get("datasets", [])[:4]:
        if not isinstance(dataset, dict):
            continue
        out["datasets"].append({
            "label": str(dataset.get("label", "Value"))[:80],
            "data": [int(value or 0) for value in dataset.get("data", [])[:20]],
        })
    return out


def choose_chart(question: str, data: Optional[Dict[str, Any]]) -> Optional[Dict[str, Any]]:
    if not data:
        return None
    q = question.lower()
    wants_table = any(word in q for word in ["list", "table", "top", "recent", "details", "detail", "record"])
    if wants_table:
        rows = _rows(data, "top_maintenance_assets") if any(word in q for word in ["maintenance", "top"]) else _rows(data, "recent_records")
        if rows:
            columns = list(rows[0].keys())[:6]
            return {
                "type": "table",
                "title": "Top Maintenance Assets" if rows == _rows(data, "top_maintenance_assets") else "Recent Records",
                "columns": columns,
                "rows": [{column: row.get(column, "") for column in columns} for row in rows[:12]],
            }
    if any(word in q for word in ["trend", "month", "monthly", "line"]):
        rows = _rows(data, "maintenance_monthly_counts")
        if rows:
            return {
                "type": "line",
                "title": "Maintenance Monthly Trend",
                "labels": [str(row.get("label", "")) for row in rows],
                "datasets": [{"label": "Maintenance", "data": [int(row.get("value") or 0) for row in rows]}],
            }
    if any(word in q for word in ["location", "site", "where"]):
        rows = _rows(data, "asset_location_counts")
        if rows:
            return {
                "type": "bar",
                "title": "Assets by Location",
                "labels": [str(row.get("label", "")) for row in rows[:10]],
                "datasets": [{"label": "Assets", "data": [int(row.get("value") or 0) for row in rows[:10]]}],
            }
    if any(word in q for word in ["type", "category", "asset type"]):
        rows = _rows(data, "asset_type_counts")
        if rows:
            return {
                "type": "bar",
                "title": "Assets by Type",
                "labels": [str(row.get("label", "")) for row in rows[:10]],
                "datasets": [{"label": "Assets", "data": [int(row.get("value") or 0) for row in rows[:10]]}],
            }
    if any(word in q for word in ["component", "item"]):
        rows = _rows(data, "component_status_counts")
        if rows:
            return {
                "type": "doughnut",
                "title": "Components by Status",
                "labels": [str(row.get("label", "")) for row in rows],
                "datasets": [{"label": "Components", "data": [int(row.get("value") or 0) for row in rows]}],
            }
    if any(word in q for word in ["ticket", "fault", "faulty", "issue", "breakdown"]):
        rows = _rows(data, "ticket_fault_counts")
        if rows:
            return {
                "type": "bar",
                "title": "Tickets / Faults",
                "labels": [str(row.get("label", "")) for row in rows[:10]],
                "datasets": [{"label": "Tickets", "data": [int(row.get("value") or 0) for row in rows[:10]]}],
            }
    if any(word in q for word in ["disposal", "dispose", "disposed", "write", "writeoff", "scrap"]):
        rows = _rows(data, "write_off_reason_counts") if "reason" in q else _rows(data, "disposal_status_counts")
        if rows:
            return {
                "type": "doughnut",
                "title": "Write-off Reasons" if "reason" in q else "Disposal Status",
                "labels": [str(row.get("label", "")) for row in rows],
                "datasets": [{"label": "Records", "data": [int(row.get("value") or 0) for row in rows]}],
            }
    if any(word in q for word in ["maintenance", "pending", "overdue", "complete"]):
        rows = _rows(data, "maintenance_type_counts") if "type" in q else _rows(data, "maintenance_status_counts")
        if rows:
            return {
                "type": "bar",
                "title": "Maintenance by Type" if "type" in q else "Maintenance by Status",
                "labels": [str(row.get("label", "")) for row in rows],
                "datasets": [{"label": "Records", "data": [int(row.get("value") or 0) for row in rows]}],
            }
    rows = _rows(data, "asset_status_counts")
    if rows:
        return {
            "type": "doughnut",
            "title": "Assets by Status",
            "labels": [str(row.get("label", "")) for row in rows],
            "datasets": [{"label": "Assets", "data": [int(row.get("value") or 0) for row in rows]}],
        }
    return None


def local_answer(question: str, data: Optional[Dict[str, Any]], data_source: str) -> str:
    total_assets = _metric(data, "total_assets")
    total_components = _metric(data, "total_components")
    open_maintenance = _metric(data, "open_maintenance")
    total_maintenance = _metric(data, "total_maintenance")
    total_tickets = _metric(data, "total_tickets")
    total_disposals = _metric(data, "total_disposals")
    lines = [
        "AI service is running without a Groq key, so this is a local system summary.",
        f"Total assets: {total_assets}",
        f"Total components: {total_components}",
        f"Maintenance records: {total_maintenance}",
        f"Open maintenance: {open_maintenance}",
        f"Tickets: {total_tickets}",
        f"Disposals: {total_disposals}",
    ]
    status_rows = _rows(data, "asset_status_counts") if data else []
    if status_rows:
        top = ", ".join(f"{row.get('label')}: {row.get('value')}" for row in status_rows[:5])
        lines.append(f"Top asset statuses: {top}.")
    lines.append("Ask a more specific question like 'show maintenance trend by month' or 'asset by location' for a focused chart.")
    return "\n".join(lines)


def ask_groq(question: str, context: str) -> str:
    if not GROQ_API_KEY:
        raise RuntimeError("GROQ_API_KEY is not configured")
    headers = {"Authorization": f"Bearer {GROQ_API_KEY}", "Content-Type": "application/json"}
    payload = {
        "model": GROQ_MODEL,
        "messages": [
            {
                "role": "system",
                "content": "You are an asset management analytics assistant. Answer using only the provided system data. Be concise, mention numbers, and suggest one practical next action when useful.",
            },
            {
                "role": "user",
                "content": f"DATA:\n{context}\n\nQUESTION: {question}",
            },
        ],
        "max_tokens": 550,
        "temperature": 0.1,
    }
    response = requests.post(GROQ_URL, headers=headers, json=payload, timeout=20)
    if response.status_code != 200:
        raise RuntimeError(f"Groq API error {response.status_code}: {response.text[:200]}")
    result = response.json()
    return result["choices"][0]["message"]["content"]


@app.post("/api/assets/chat", response_model=ChatResponse)
async def assets_chat(message: ChatMessage):
    try:
        question = message.message.strip()
        if not question:
            raise HTTPException(status_code=422, detail="Message is required")
        if len(question) > MAX_QUESTION_CHARS:
            raise HTTPException(status_code=422, detail=f"Message must be {MAX_QUESTION_CHARS} characters or fewer")
        data_source = message.data_source or "overall"
        context = trim_context(build_context(message.data, data_source))
        chart = safe_chart(choose_chart(question, message.data))
        try:
            answer = ask_groq(question, context)
        except Exception as exc:
            answer = local_answer(question, message.data, data_source)
            answer += f"\n\nLLM note: {exc}"
        return ChatResponse(
            response=answer,
            timestamp=datetime.now().isoformat(),
            data_source=data_source,
            chart=chart,
        )
    except HTTPException:
        raise
    except Exception as exc:
        raise HTTPException(status_code=500, detail=str(exc))


@app.get("/api/assets/status")
async def assets_status():
    return {
        "status": "ready" if GROQ_API_KEY else "degraded",
        "ai_service": "Groq",
        "model": GROQ_MODEL,
        "groq_configured": bool(GROQ_API_KEY),
        "timestamp": datetime.now().isoformat(),
    }


@app.get("/")
async def root():
    return {
        "name": "Assets AI Insights API",
        "status": "running",
        "endpoints": {"chat": "/api/assets/chat", "status": "/api/assets/status"},
    }


if __name__ == "__main__":
    import uvicorn

    uvicorn.run(app, host="0.0.0.0", port=8002, log_level="info")
