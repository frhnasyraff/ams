<div class="ai-insights <?= !empty($embed) ? 'ai-insights--embed' : '' ?>" data-query-url="<?= htmlspecialchars($query_url, ENT_QUOTES, 'UTF-8') ?>" data-status-url="<?= htmlspecialchars($status_url, ENT_QUOTES, 'UTF-8') ?>">
  <div class="ai-insights__header">
    <div>
      <h1>AI Insights</h1>
      <p>Ask about assets, maintenance, components, locations, and trends.</p>
    </div>
    <div class="ai-insights__status" data-ai-status>
      <span></span>
      <strong>Checking AI</strong>
    </div>
  </div>

  <div class="ai-insights__layout">
    <section class="ai-insights__chat" aria-label="AI chat">
      <div class="ai-insights__messages" data-ai-messages>
        <div class="ai-message ai-message--assistant">
          <div class="ai-message__avatar"><i class="fas fa-chart-line"></i></div>
          <div class="ai-message__bubble">
            <strong>Ready.</strong>
            <p>Ask for a summary, trend, breakdown, or top list.</p>
          </div>
        </div>
      </div>

      <div class="ai-insights__suggestions">
        <button type="button" data-ai-suggestion="Show asset status breakdown">Asset status</button>
        <button type="button" data-ai-suggestion="Show maintenance trend by month">Maintenance trend</button>
        <button type="button" data-ai-suggestion="Show assets by location">Locations</button>
        <button type="button" data-ai-suggestion="List top maintenance assets">Top list</button>
      </div>

      <form class="ai-insights__form" data-ai-form>
        <input type="text" data-ai-input placeholder="Ask anything about the system..." autocomplete="off">
        <button type="submit"><i class="fas fa-paper-plane"></i><span>Send</span></button>
      </form>
    </section>

    <aside class="ai-insights__chart" aria-label="AI chart output">
      <div class="ai-insights__chart-head">
        <div>
          <p class="ai-insights__eyebrow">Graph</p>
          <h2 data-ai-chart-title>No graph yet</h2>
        </div>
      </div>
      <div class="ai-insights__chart-body">
        <canvas data-ai-chart></canvas>
        <div class="ai-insights__table" data-ai-table></div>
        <div class="ai-insights__empty-chart" data-ai-empty-chart>
          <i class="fas fa-chart-pie"></i>
          <p>Graphs appear when the answer has visual data.</p>
        </div>
      </div>
    </aside>
  </div>
</div>
