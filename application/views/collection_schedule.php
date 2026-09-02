<section class="service_type">
    <div class="container">
        <form action="<?= site_url('collection_schedule/update') ?>" method="post">
            <div class="input">
                <label for="">Maximum Weekly Frequency</label>
                <input type="text" class="field" name="weekly_frequency" value="<?= $maxFrequency->weekly ?>">
            </div>
            <div class="input">
                <label for="">Maximum Monthly Frequency</label>
                <input type="text" class="field" name="monthly_frequency" value="<?= $maxFrequency->monthly ?>">
            </div>
            <div class="text-center">
                <button type="submit" class="scheduleupdatebtn">Update</button>
            </div>
        </form>
    </div>
</section>