<?php
@include '../components/connection.php';  // Make sure this path is correct
session_start();

$landholder_id = $_SESSION['landholder_id'];

// Fetch users for the landholder with existing chats
try {
    $users = $conn->query("
        SELECT DISTINCT u.user_id, u.username, u.full_name, u.profile_picture 
        FROM users_tb u
        JOIN messages_tb m ON (u.user_id = m.sender_id OR u.user_id = m.receiver_id)
        WHERE (m.sender_id = $landholder_id AND m.sender_type = 'landholder') 
           OR (m.receiver_id = $landholder_id AND m.receiver_type = 'landholder')
    ");
} catch (Exception $e) {
    die('Error fetching users: ' . $e->getMessage());
}

// Fetch all users for starting a new chat
try {
    $all_users = $conn->query("SELECT user_id, username, full_name, profile_picture FROM users_tb");
} catch (Exception $e) {
    die('Error fetching all users: ' . $e->getMessage());
}

// Messaging logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_text'])) {
    $stmt = $conn->prepare("INSERT INTO messages_tb (sender_id, receiver_id, sender_type, receiver_type, message_text) VALUES (?, ?, 'landholder', 'user', ?)");
    $stmt->bindParam(1, $landholder_id);
    $stmt->bindParam(2, $_POST['receiver_id']);
    $stmt->bindParam(3, $_POST['message_text']);
    $stmt->execute();
}

// Fetch messages and user details if a specific chat is selected
$messages = [];
$user_details = null;
if (isset($_GET['receiver_id'])) {
    $user_id = $_GET['receiver_id'];
    // Fetch user details
    $stmt = $conn->prepare("SELECT full_name, profile_picture FROM users_tb WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user_details = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch messages
    $sql = "SELECT * FROM messages_tb WHERE (receiver_id = ? AND receiver_type = 'user' AND sender_id = ? AND sender_type = 'landholder') OR (sender_id = ? AND sender_type = 'user' AND receiver_id = ? AND receiver_type = 'landholder') ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $landholder_id, $user_id, $landholder_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landholder Chat System</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Include jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <style>
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 400px;
            overflow-y: scroll;
        }

        .chat-message {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            margin-bottom: 10px;
        }

        .chat-message.landholder {
            align-items: flex-end;
        }

        .chat-bubble {
            max-width: 60%;
            padding: 10px;
            border-radius: 20px;
            color: #fff;
        }

        .chat-bubble.landholder {
            background-color: #007bff;
        }

        .chat-bubble.user {
            background-color: #d3d3d3;
            color: #000;
            
        }

        .timestamp {
            font-size: 0.75rem;
            color: #666;
            margin-top: 2px;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .user-info img {
            border-radius: 50%;
            width: 50px;
            height: 50px;   
            margin-right: 10px;
        }

        .user-info h5 {
            margin: 0;
        }
    </style>
</head>

<body class="bg-gray-100 font-family-karla flex">
    <?php include 'landholder-header.php'; ?>

    <div class="container mt-3 overflow-y-auto">
        <div class="row">
            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-3 mb-3">
                    <h4 class="border-bottom pb-2 mb-0">Users</h4>
                    <div class="list-group mt-3">
                        <?php foreach ($users as $user) : ?>
                            <a href="?receiver_id=<?= $user['user_id'] ?>" class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($user['username']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <!-- New Chat Button -->
                    <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#newChatModal">
                        New Chat
                    </button>
                </div>
            </div>
            <div class="col-md-8">
                <div class="bg-white rounded shadow-sm p-3">
                    <h4 class="border-bottom pb-2 mb-3">Chat</h4>
                    <?php if ($user_details) : ?>
                        <div class="user-info mb-3">
                            <img src="../uploaded_image/<?= htmlspecialchars($user_details['profile_picture']) ?>" alt="<?= htmlspecialchars($user_details['full_name']) ?>'s Profile Picture">
                            <h5><?= htmlspecialchars($user_details['full_name']) ?></h5>
                        </div>
                    <?php endif; ?>
                    <div class="chat-container">
                        <?php foreach ($messages as $message) : ?>
                            <div class="chat-message <?= $message['sender_type'] === 'landholder' ? 'landholder' : 'user' ?>">
                                <div class="chat-bubble <?= $message['sender_type'] === 'landholder' ? 'landholder' : 'user' ?>">
                                    <?= htmlspecialchars($message["message_text"]) ?>
                                </div>
                                <div class="timestamp"><?= date("g:i A", strtotime($message["timestamp"])) ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($messages)) : ?>
                            <p class="text-center mt-5">No messages yet.</p>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($_GET['receiver_id'])) : ?>
                        <form method="POST" class="mt-3 d-flex">
                            <input type="hidden" name="sender_id" value="<?= $landholder_id ?>">
                            <input type="hidden" name="receiver_id" value="<?= $_GET['receiver_id'] ?>">
                            <textarea name="message_text" class="form-control me-2" placeholder="Write your message here..." required></textarea>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for New Chat -->
    <div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newChatModalLabel">Start New Chat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group">
                        <?php foreach ($all_users as $user) : ?>
                            <li class="list-group-item">
                                <a href="?receiver_id=<?= $user['user_id'] ?>" class="stretched-link">
                                    <img src="../uploaded_image/<?= htmlspecialchars($user['profile_picture']) ?>" alt="<?= htmlspecialchars($user['full_name']) ?>'s Profile Picture" style="width: 30px; height: 30px; border-radius: 50%;">
                                    <?= htmlspecialchars($user['full_name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
</body>

</html>

<!-- Place this script at the end of your HTML body -->
<script>
    // Function to scroll the chat container to the bottom
    function scrollToBottom() {
        var chatContainer = document.querySelector('.chat-container');
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }

    // Call scrollToBottom when the page loads
    window.addEventListener('load', function() {
        scrollToBottom();
    });

    // Call scrollToBottom after sending a message
    document.querySelector('form').addEventListener('submit', function() {
        scrollToBottom();
    });
</script>