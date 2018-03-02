<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="user_id" content="<?= $current_user->id ?>" />
        <meta name="company_id" content="<?= $company_id ?>" />
        <meta name="general_conversation" content="<?= $general_conversation ?>" />
        <title>Chat</title>
        <base href="<?= base_url() ?>" />
        <link rel="stylesheet" type="text/css" href="http://localhost/resto/assets/css/flavored-reset-and-normalize.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/restp/assets/css/bootstrap.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/resto/assets/css/font-awesome.min.css" />
        <link rel="stylesheet" type="text/css" href="http://localhost/resto/assets/css/styles.css" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/shimmer.css') ?>" />
        <link rel="stylesheet" type="text/css" href="<?= base_url('assets/css/styles.css') ?>" />
    </head>
    <body>
        <div id="sidebar">
            <div class="shimmer shimmer--dark m-2"></div>
            <div class="shimmer shimmer--dark-secondary w-75 m-2 mb-3"></div>

            <div class="shimmer shimmer--dark m-2"></div>
            <div class="shimmer shimmer--dark-secondary w-75 m-2"></div>
            <div class="sidebar__header"></div>
            <ul id="conversationList"></ul>
        </div>
        <div class="main-content">
            <div class="topbar">
                <nav class="navbar navbar-expand-lg navbar-custom justify-content-between">
                    <div id="nav-icon-open" class="custom-toggle hidden-toggle">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="navbar-brand">
                        <div class="shimmer shimmer--light w-100 m-2"></div>
                    </div>
                    <div class="nav">
                        <div class="shimmer shimmer--light w-100 m-2"></div>
                    </div>
                </nav>
            </div>

            <div class="container-fluid" id="messageArea">
                <div class="shimmer shimmer--light w-50 m-2 mt-3"></div>
                <div class="shimmer shimmer--light w-25 m-2"></div>

                <div class="shimmer shimmer--light w-50 m-2 mt-4"></div>
                <div class="shimmer shimmer--light w-25 m-2"></div>
            </div>

            <div class="bottombar">
                <nav class="navbar navbar-custom">
                    <form class="form-inline w-100" id="createMessageForm">
                        <input class="form-control" id="messageBody" name="body" autocomplete="off" placeholder="Type a message..." maxlength="100" />
                        <button class="btn btn-link" type="submit">
                            <i class="fa fa-send"></i>
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <div class="modal fade" id="createConversationModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">New conversation</div>
                    <div class="modal-body">
                        <div class="menu" id="conversationList"></div>
                    </div>
                </div>
            </div>
        </div>

        <script src="http://localhost/resto/assets/js/jquery.js"></script>
        <script src="http://localhost/resto/assets/js/popper.min.js"></script>
        <script src="http://localhost/resto/assets/js/bootstrap.min.js"></script>
        <script src="http://localhost/resto/assets/js/jquery.nicescroll.min.js"></script>
        <script src="http://localhost/resto/assets/js/script.js"></script>
        <script src="<?= base_url('assets/js/socket.io.js') ?>"></script>
        <script src="<?= base_url('assets/js/moment.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/page.js') ?>"></script>
        <script src="<?= base_url('assets/js/querystring.min.js') ?>"></script>
        <script src="<?= base_url('assets/js/jquery-plugin.js') ?>"></script>
        <script src="<?= base_url('assets/js/app.js') ?>"></script>
    </body>
</html>