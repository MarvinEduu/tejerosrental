<?php
@include '../components/connection.php';  // Make sure this path is correct
session_start();

$landholder_id = $_SESSION['landholder_id'];

// Fetch users for the landholder to chat with
try {
    $users = $conn->query("SELECT user_id, username FROM users_tb");
} catch (Exception $e) {
    die('Error fetching users: ' . $e->getMessage());
}

// Messaging logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message_text'])) {
    $stmt = $conn->prepare("INSERT INTO messages_tb (sender_id, receiver_id, sender_type, receiver_type, message_text) VALUES (?, ?, 'landholder', 'user', ?)");
    $stmt->bindParam(1, $landholder_id);
    $stmt->bindParam(2, $_POST['receiver_id']);
    $stmt->bindParam(3, $_POST['message_text']);
    $stmt->execute();
}

// Fetch messages if a specific chat is selected
$messages = [];
if (isset($_GET['receiver_id'])) {
    $sql = "SELECT * FROM messages_tb WHERE (receiver_id = ? AND receiver_type = 'user' AND sender_id = ? AND sender_type = 'landholder') OR (sender_id = ? AND sender_type = 'user' AND receiver_id = ? AND receiver_type = 'landholder') ORDER BY timestamp ASC";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$_GET['receiver_id'], $landholder_id, $_GET['receiver_id'], $landholder_id]);
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
            align-items: flex-start; /* Align items to the left by default */
            margin-bottom: 10px;
        }
        .chat-message.landholder {
            align-items: flex-end; /* Align landholder messages to the right */
        }
        .chat-bubble {
            max-width: 60%;
            padding: 10px;
            border-radius: 20px;
            color: #fff;
        }
        .chat-bubble.landholder {
            background-color: #007bff; /* Blue for landholder */
        }
        .chat-bubble.user {
            background-color: #6c757d; /* Grey for user */
        }
        .timestamp {
            font-size: 0.75rem;
            color: #666;
            margin-top: 2px;
        }
    </style>

</head>
<body  class="bg-gray-100 font-family-karla flex">
    <?php include 'landholder-header.php'; ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-4">
                <div class="bg-white rounded shadow-sm p-3 mb-3">
                    <h4 class="border-bottom pb-2 mb-0">Users</h4>
                    <div class="list-group mt-3">
                        <?php foreach ($users as $user): ?>
                            <a href="?receiver_id=<?= $user['user_id'] ?>" class="list-group-item list-group-item-action">
                                <?= htmlspecialchars($user['username']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="bg-white rounded shadow-sm p-3">
                    <h4 class="border-bottom pb-2 mb-3">Chat</h4>
                    <div class="chat-container">
                        <?php foreach ($messages as $message): ?>
                            <div class="chat-message <?= $message['sender_type'] === 'landholder' ? 'landholder' : 'user' ?>">
                                <div class="chat-bubble <?= $message['sender_type'] === 'landholder' ? 'landholder' : 'user' ?>">
                                    <?= htmlspecialchars($message["message_text"]) ?>
                                </div>
                                <div class="timestamp"><?= date("g:i A", strtotime($message["timestamp"])) ?></div>
                            </div>
                        <?php endforeach; ?>
                        <?php if (empty($messages)): ?>
                            <p class="text-center mt-5">No messages yet.</p>
                        <?php endif; ?>
                    </div>
                    <?php if (isset($_GET['receiver_id'])): ?>
                        <form method="POST" class="mt-3">
                            <input type="hidden" name="sender_id" value="<?= $landholder_id ?>">
                            <input type="hidden" name="receiver_id" value="<?= $_GET['receiver_id'] ?>">
                            <textarea name="message_text" class="form-control mb-2" placeholder="Write your message here..." required></textarea>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    <?php endif; ?>
                </div>
                    </div>
        </div>
        </div>
<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
