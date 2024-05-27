<?php
@include 'components/connection.php';
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: login.php');
    exit; // Stop further script execution
}

// Fetch landholders for the user to chat with
try {
    $landholders = $conn->query("SELECT landholder_id, username, full_name FROM landholders_tb");
} catch (Exception $e) {
    die('Error fetching landholders: ' . $e->getMessage());
}

// Messaging logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_text'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message_text = $_POST['message_text'];

    // Insert new message into the database
    $stmt = $conn->prepare("INSERT INTO messages_tb (sender_id, receiver_id, sender_type, receiver_type, message_text) VALUES (?, ?, 'user', 'landholder', ?)");
    $stmt->bindParam(1, $sender_id);
    $stmt->bindParam(2, $receiver_id);
    $stmt->bindParam(3, $message_text);
    $stmt->execute();

    // Fetch the latest message ID in the conversation
    $latest_message_id = $conn->lastInsertId();

    // No header redirect here

    // Set a JavaScript script to update the URL
    echo '<script>
            const receiverId = ' . json_encode($receiver_id) . ';
            const latestMessageId = ' . json_encode($latest_message_id) . ';
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set("receiver_id", receiverId);
            urlParams.set("message_id", latestMessageId);
            urlParams.set("sent", "true");
            const newUrl = window.location.pathname + "?" + urlParams.toString();
            window.history.replaceState({ path: newUrl }, "", newUrl);
          </script>';
}

// Fetch messages if a specific chat is selected
$messages = [];
if (isset($_GET['receiver_id'])) {
    $receiver_id = $_GET['receiver_id'];

    $sql = "SELECT * FROM messages_tb WHERE (receiver_id = ? AND receiver_type = 'landholder' AND sender_id = ? AND sender_type = 'user') OR (sender_id = ? AND sender_type = 'landholder' AND receiver_id = ? AND receiver_type = 'user') ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$receiver_id, $_SESSION['user_id'], $_SESSION['user_id'], $receiver_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Chat System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="images/logoer.png">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        .chat-bubble {
            max-width: 60%;
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 2px;
            color: #fff;
        }

        .chat-start .chat-bubble {
            background-color: #007bff; /* Blue background for user */
            align-self: flex-end;
            margin-left: auto;
            margin-right: 10px;
        }

        .chat-end .chat-bubble {
            background-color: #6c757d; /* Grey background for landholder */
            align-self: flex-start;
            margin-left: 10px;
            margin-right: auto;
        }
    </style>
</head>

<body>
    <?php include 'user/user-header.php' ?>
    <div class="container-fluid p-10">
        <div class="row">
            <div class="col-4">
                <h4>Landholders</h4>
                <div class="list-group">
                    <?php foreach ($landholders as $landholder): ?>
                        <a href="?receiver_id=<?= $landholder['landholder_id'] ?>" class="list-group-item list-group-item-action">
                            <img src="uploaded_image/<?= $landholder['profile_picture'] ?>" alt="profile_picture" class="profile-picture">
                            <?= $landholder['username'] ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <!-- Button to open modal -->
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#allLandholdersModal">
                    All Landholders
                </button>
            </div>

            <div class="col-8">
                <h4 class="text-2xl">Chat</h4>
                <div class="chat-box" style="height: 400px; overflow-y: scroll;">
                    <?php foreach ($messages as $message): ?>
                        <div class="chat <?= $message['sender_type'] == 'user' ? 'chat-end' : 'chat-start' ?>">
                            <div class="chat-bubble p-3 m-4">
                                <?= htmlspecialchars($message["message_text"]) ?> <small>(<?= $message["timestamp"] ?>)</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($messages)): ?>
                        <p>No messages yet.</p>
                    <?php endif; ?>
                </div>

                <?php if (isset($_GET['receiver_id'])): ?>
                    <form method="POST" id="messageForm" class="d-flex align-items-center">
                        <input type="hidden" name="sender_id" value="<?= $_SESSION['user_id'] ?>">
                        <input type="hidden" name="receiver_id" value="<?= $_GET['receiver_id'] ?>">
                        <textarea name="message_text" class="form-control mb-2 me-2" placeholder="Write your message here..." required></textarea>
                        <button type="submit" class="btn btn-primary">Send Message</button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="allLandholdersModal" tabindex="-1" aria-labelledby="allLandholdersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="allLandholdersModalLabel">All Landholders</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php foreach ($landholders as $landholder): ?>
                            <li class="list-group-item">
                                <a href="?receiver_id=<?= $landholder['landholder_id'] ?>" class="stretched-link">
                                    <img src="uploaded_image/<?= $landholder['profile_picture'] ?>" alt="Profile Picture" class="profile-picture">
                                    <?= $landholder['username'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php include 'user/user-footer.php' ?>

    <script>
        document.getElementById('messageForm').addEventListener('submit', function(event) {
            // No need to prevent default form submission
            // Form submission will proceed as normal

            // Dynamically update the URL to maintain state
            const receiverId = document.querySelector('input[name="receiver_id"]').value;

            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set('receiver_id', receiverId);

            const newUrl = `${window.location.pathname}?${urlParams.toString()}`;
            window.history.replaceState({ path: newUrl }, '', newUrl);
        });
    </script>

</body>
</html>
