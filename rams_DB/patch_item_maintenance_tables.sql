-- Component maintenance history and task details used by Items AJAX.
CREATE TABLE IF NOT EXISTS logs_item_maintenance (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 item_ticket_id INT UNSIGNED NOT NULL,
 update_date DATETIME NULL,
 final_status VARCHAR(100) NULL,
 created_at DATETIME NULL,
 updated_at DATETIME NULL,
 KEY item_ticket_idx (item_ticket_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS logs_item_maintenance_task_done (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 item_maintenance_id INT UNSIGNED NOT NULL,
 task_done TEXT NULL,
 remarks TEXT NULL,
 created_at DATETIME NULL,
 updated_at DATETIME NULL,
 KEY item_maintenance_idx (item_maintenance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;