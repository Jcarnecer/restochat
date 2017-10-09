<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="user_id" content="<?= $current_user->id ?>" />
        <meta name="company_id" content="<?= $company_id ?>" />
        <meta name="general_conversation" content="<?= $general_conversation ?>" />
        <base href="/chat-alpha/" />
        <link rel="stylesheet" type="text/css" href="http://localhost/login/assets/css/flavored-reset-and-normalize.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/login/assets/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/login/assets/css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/login/assets/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/shimmer.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/styles.css') ?>" />
    </head>
    <body>
        <div id="sidebar">
            <div class="shimmer shimmer--dark m-2"></div>
            <div class="shimmer shimmer--dark-secondary w-75 m-2 mb-3"></div>

            <div class="shimmer shimmer--dark m-2"></div>
            <div class="shimmer shimmer--dark-secondary w-75 m-2"></div>
            <ul class="conversation-list"></ul>
        </div>
        <div class="main-content">
            <div class="topbar">
                <nav class="navbar navbar-expand-lg navbar-custom justify-content-between">
                    <div id="nav-icon-open" class="custom-toggle hidden-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="navbar-brand w-25">
                        <div class="shimmer shimmer--light w-100 m-2"></div>
                    </div>
                    <div class="nav">
                        <div class="shimmer shimmer--light w-100 m-2"></div>
                    </div>
                </nav>
            </div>

            <div class="container-fluid message-area">
                <div class="shimmer shimmer--light w-50 m-2 mt-3"></div>
                <div class="shimmer shimmer--light w-25 m-2"></div>

                <div class="shimmer shimmer--light w-50 m-2 mt-4"></div>
                <div class="shimmer shimmer--light w-25 m-2"></div>
            </div>

            <div class="bottombar">
                <nav class="navbar navbar-custom">
                    <form class="form-inline w-100" id="createMessageForm">
                        <input class="form-control" name="body" autocomplete="off" placeholder="Type a message..." maxlength="100" />
                        <button class="btn btn-link" type="submit">
                            <i class="fa fa-send"></i>
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <div class="modal" id="createConversationModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">New Message</div>
                        <div class="modal-body">
                            <form id="createConversationForm">
                                <div class="form-group participants">
                                    <label>Send message to:</label>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="body" placeholder="Type your message here..."></textarea>
                                </div>
                            </form>
                        </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit" form="createConversationForm">Send</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="http://localhost/login/assets/js/jquery.js"></script>
        <script src="http://localhost/login/assets/js/popper.min.js"></script>
        <script src="http://localhost/login/assets/js/bootstrap.min.js"></script>
        <script src="http://localhost/login/assets/js/jquery.nicescroll.min.js"></script>
        <script src="http://localhost/login/assets/js/script.js"></script>
        <script src="<?= base_url('assets/js/socket.io.js') ?>"></script>
        <script src="<?= base_url('assets/js/moment.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/page.js') ?>"></script>
        <script src="<?= base_url('assets/js/ajax.js') ?>"></script>
        <script src="<?= base_url('assets/js/jquery-plugin.js') ?>"></script>
        <script src="<?= base_url('assets/js/page.routes.js') ?>"></script>
        <script src="<?= base_url('assets/js/app.js') ?>"></script>
    </body>
</html>