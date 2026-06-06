<div class="modal fade"
     id="<?= esc($id) ?>"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog <?= esc($size) ?>">

        <div class="modal-content">

            <form
                action="<?= esc($action) ?>"
                method="post"
                enctype="multipart/form-data">

                <?= csrf_field() ?>

                <div class="modal-header">

                    <h5 class="modal-title">
                        <?= esc($title) ?>
                    </h5>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>

                </div>

                <div class="modal-body">

                    <?= $content ?>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-dark">
                        Save
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>