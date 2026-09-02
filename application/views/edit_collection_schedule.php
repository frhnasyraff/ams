<div class="serviceModal">
    <div class="modal-dialog modal-lg mt-0">
        <div class="modal-content">
            <div class="modal-body">
                <div class="container service-container">
                    <h2 class="heading">Edit Collection Schedule</h2>
                    <form action="<?= site_url("collection_schedule/updateCollectionScheduleMonthly"); ?>" method="post">
                        <input type="hidden" name="edit_collection_schedule_id" value="<?= $this->input->get('id') ?>">
                        <div class="form-group">
                            <label for="">Frequency</label>
                            <input type="text" name="frequency" id="frequency" value="<?= $collectionSchedule->frequency ?>" class="form-control" placeholder="Frequency" required>
                        </div>

                        <div class="form-group" id="month_dates">
                            <div class="date">
                                <label for="">Days in month <i id="add-more-dates" class="fa fa-plus add-more"></i></label>
                            </div>
                            <div id="more-month-dates">
                                <?php foreach ($collectionScheduleDates as $collectionSchedule) { ?>
                                    <div class="date-remove">
                                        <select name="dates[]" id="first-month-date" class="form-control mb-1">
                                            <?php foreach (range(1, 31) as $day) { ?>
                                                <option value="<?= $day ?>" <?= $collectionSchedule->date == $day ? 'selected' : '' ?>><?= $day ?></option>
                                            <?php } ?>
                                        </select>
                                        <i class="fa fa-times remove-more"></i>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="form-group text-center">
                            <button type="submit" class="submit-btn">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>