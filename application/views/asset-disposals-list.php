<div class="container-fluid disposal-list-page">
    <section class="disposal-list-hero">
        <div class="disposal-list-heading">
            <span class="disposal-list-icon"><i class="fas fa-recycle"></i></span>
            <div>
                <span class="disposal-list-eyebrow">Asset Lifecycle</span>
                <h2>Disposals List</h2>
                <p>Track write-off requests, approval status and disposal records in one place.</p>
            </div>
        </div>
        <div class="disposal-list-actions">
            <div class="disposal-total">
                <strong><?php echo (int) $total_rows; ?></strong>
                <span>Total Requests</span>
            </div>
            <a href="<?php echo site_url('asset_disposal_requests'); ?>" class="btn disposal-create-btn">
                <i class="fas fa-plus"></i> New Request
            </a>
        </div>
    </section>

    <section class="disposal-filter-bar">
        <form method="get" action="<?php echo site_url('asset_disposals'); ?>">
            <div class="disposal-filter-search">
                <i class="fas fa-search"></i>
                <input type="search" name="search" value="<?php echo htmlspecialchars((string) ($search ?? '')); ?>"
                    placeholder="Search request, asset or method">
            </div>
            <select name="status" aria-label="Filter by status">
                <?php
                $status_options = [
                    'all' => 'All Statuses',
                    'new' => 'New',
                    'draft' => 'Draft',
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected'
                ];
                foreach ($status_options as $value => $label):
                ?>
                    <option value="<?php echo $value; ?>" <?php echo (($status ?: 'all') === $value) ? 'selected' : ''; ?>>
                        <?php echo $label; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn disposal-filter-btn"><i class="fas fa-sliders-h"></i> Apply</button>
            <?php if (!empty($search) || (!empty($status) && $status !== 'all')): ?>
                <a href="<?php echo site_url('asset_disposals'); ?>" class="btn disposal-reset-btn">Reset</a>
            <?php endif; ?>
        </form>
    </section>

    <section class="disposal-table-card">
        <div class="disposal-table-header">
            <div>
                <h5><i class="fas fa-list-check"></i> Disposal Records</h5>
                <small><?php echo (int) $total_rows; ?> record<?php echo ((int) $total_rows === 1) ? '' : 's'; ?> found</small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table disposal-table">
                <thead>
                    <tr>
                        <th>Request</th>
                        <th>Asset ID</th>
                        <th>Asset Name</th>
                        <th>Asset Type</th>
                        <th>Method</th>
                        <th>Estimated Value</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($disposals)): ?>
                        <?php foreach ($disposals as $d): ?>
                            <?php $status_class = preg_replace('/[^a-z0-9-]/', '-', strtolower((string) ($d->status ?? 'new'))); ?>
                            <tr>
                                <td><strong class="disposal-request-number"><?php echo htmlspecialchars((string) ($d->request_number ?? '—')); ?></strong></td>
                                <td><?php echo htmlspecialchars((string) ($d->equipment_id ?? '—')); ?></td>
                                <td><strong><?php echo htmlspecialchars((string) ($d->equipment_name ?? 'Unknown Asset')); ?></strong></td>
                                <td><span class="disposal-type"><?php echo htmlspecialchars((string) ($d->equipment_type_name ?? 'Uncategorized')); ?></span></td>
                                <td><?php echo htmlspecialchars((string) ($d->disposal_method_name ?? 'N/A')); ?></td>
                                <td>RM <?php echo number_format((float) ($d->estimated_value ?? 0), 2); ?></td>
                                <td><span class="disposal-status status-<?php echo $status_class; ?>"><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', (string) ($d->status ?? 'new')))); ?></span></td>
                                <td><?php echo !empty($d->created_at) ? date('d M Y, H:i', strtotime($d->created_at)) : '—'; ?></td>
                                <td>
                                    <a href="<?php echo site_url('asset_disposal_requests/view/' . $d->id); ?>" class="btn disposal-view-btn" title="View request">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="disposal-empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <strong>No disposal records found</strong>
                                    <span>Try changing the filters or create a new request.</span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($pagination)): ?>
            <div class="disposal-pagination"><?php echo $pagination; ?></div>
        <?php endif; ?>
    </section>
</div>
