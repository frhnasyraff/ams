<div class="row">
    <div class="col-sm-4 color">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Reset your password</h6>
            </div>
            <div class="card-body">
                <form class="form-horizontal" action="<?=site_url("user/reset_password");?>" method="post">
                    <?=$this->steve->form_group_label_input("password", "existing_password", "Existing password", "", 1);?>
                    <?=$this->steve->form_group_label_input("password", "password", "New password", "", 1);?>
                    <?=$this->steve->form_group_label_input("password", "confirm_password", "Re-enter new password", "", 1);?>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">Update password</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Profile picture</h6>
            </div>
            <div class="card-body">
                <p class="text-info">Please ensure the photo is a square for best results.</p>
                <div class="row">
                    <div class="col-md-8">
                        <form action="<?=site_url("user/upload_picture");?>" class="dropzone"
                            enctype="multipart/form-data">
                            <div class="fallback">
                                <input name="file[]" type="file" accept="image/*" />
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <?php if ($_SESSION['user']->profile_picture) { ?>
                        <img class="rounded-circle img-thumbnail"
                            src="<?= site_url("storage/User-" . $_SESSION['user']->user_id . "/" . $_SESSION['user']->profile_picture); ?>" />
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="col-sm-8">

    <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Default font</h6>
            </div>
            <div class="card-body text-center">
                <input id="font" type="text" class="form-control" />
                <br />
                <div class="btn btn-secondary mb-t reset_font">Reset to default</div>
            </div>
        </div>
        
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Default colour</h6>
            </div>
            <div class="card-body">
                <div class="row colorPicker">
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#84DE02">Alien Armpit
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#E88E5A">Big Foot Feet
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FF8833">Grandma's Perfume
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#C53151">Dingy Dungeon
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#E97451">Baseball Mitt
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#B05C52">Giant's Club
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FF4466">Magic Potion
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#828E84">Mummy's Tomb
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FD5240">Ogre Odor</div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#391285">Pixie Powder
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FF85CF">Princess Perfume
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FF4681">Sasquatch Socks
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#4BC7CF">Sea Serpent</div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FF6D3A">Smashed Pumpkin
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#FF404C">Sunburnt Cyclops
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#3F26BF">Ultramarine Blue
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#EF7F01">Winnie the Pooh 1
                    </div>
                    <div class="btn offset-sm-1 btn-sm mb-2 col-sm-4 color" style="background:#F52532">Winnie the Pooh 2
                    </div>

                    <div class="btn btn-secondary offset-sm-3 mb-2 col-sm-5 reset_color">Reset to default</div>
                </div>
                <div class="text-center colorwheel">
                    <input id="color-block" type="text" value="#ff8800" data-wheelcolorpicker="" data-wcp-format="css"
                        data-wcp-layout="block" data-wcp-sliders="wsvp" data-wcp-cssclass="color-block"
                        data-wcp-autoresize="false" />
                </div>
            </div>
        </div>

    </div>
</div>