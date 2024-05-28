<?php
@include 'components/connection.php';
session_start();

// Check if user is not logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page
    header('Location: login.php');
    exit; // Stop further script execution
}

// Fetch user details
try {
    $stmt = $conn->prepare("SELECT * FROM users_tb WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching user details: ' . $e->getMessage());
}

// Fetch landholders the user has previously chatted with, ordered by latest chat time
try {
    $stmt = $conn->prepare("
        SELECT DISTINCT lh.landholder_id, lh.username, lh.full_name, lh.profile_picture, MAX(m.timestamp) AS latest_chat_time
        FROM landholders_tb lh
        JOIN messages_tb m ON (lh.landholder_id = m.sender_id OR lh.landholder_id = m.receiver_id)
        WHERE (m.sender_id = :user_id AND m.sender_type = 'user') 
           OR (m.receiver_id = :user_id AND m.receiver_type = 'user')
        GROUP BY lh.landholder_id, lh.username, lh.full_name, lh.profile_picture
        ORDER BY latest_chat_time DESC
    ");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $landholders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching landholders: ' . $e->getMessage());
}



// Fetch all landholders for new chat modal
try {
    $stmt = $conn->prepare("SELECT * FROM landholders_tb");
    $stmt->execute();
    $all_landholders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    die('Error fetching all landholders: ' . $e->getMessage());
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
    $user_id = $_SESSION['user_id'];

    $sql = "SELECT * FROM messages_tb WHERE 
            (sender_id = ? AND sender_type = 'user' AND receiver_id = ? AND receiver_type = 'landholder') 
            OR 
            (sender_id = ? AND sender_type = 'landholder' AND receiver_id = ? AND receiver_type = 'user') 
            ORDER BY timestamp ASC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $receiver_id, $receiver_id, $user_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en" data-theme="winter">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landholder Chat System</title>
    <link rel="stylesheet" href="css/style.css">
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Include jQuery and DaisyUI CSS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.8.0/dist/full.min.css" rel="stylesheet" type="text/css">


    <style>
        /* Your custom CSS remains the same */
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

        .profile-img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
            margin-bottom: 12px;
        }

        .list-group-item-action {
            display: flex;
            align-items: center;
        }

        /* Styles for the modal overlay */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            /* Semi-transparent black */
            z-index: 1000;
            /* Ensure the modal is on top of other elements */
            display: none;
            /* Initially hidden */
        }

        /* Styles for the modal */
        .modal {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            max-width: 80%;
            margin: 100px auto;
            /* Center the modal vertically and horizontally */
            padding: 20px;
            overflow: auto;
        }

        /* Additional styling for modal content */
        .modal-content {
            max-height: 80vh;
            /* Limit height of the modal content */
        }

        /* Styles for the close button */
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
        }

        /* Additional styling for the modal content */
        .modal-content {
            padding: 20px;
        }

        .overflow-y-auto {
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <?php include 'user/user-header.php'; ?>

    <div class="container mt-3 overflow-y-auto">
        <div class="grid grid-cols-12 gap-4">
            <div class="col-span-4  my-8">
                <div class="bg-white rounded shadow-md p-3 ml-12 mb-18 h-full border border-gray-500">
                    <div class="flex justify-between items-center">
                        <h4 class="border-b pb-2 mb-0">Landholders</h4>
                        <!-- Plus button to show all landholders -->
                        <button id="showAllLandholders" class="btn btn-primary">Show All Landholders</button>
                    </div>
                    <div class="list-group mt-3 overflow-y-auto" id="landholdersList" style="max-height: 400px;">
                        <?php foreach ($landholders as $landholder) : ?>
                            <?php
                            // Format the last chat date and time
                            $last_chat_datetime = date("M j, Y g:i A", strtotime($landholder['latest_chat_time']));
                            ?>
                            <a href="?receiver_id=<?= $landholder['landholder_id'] ?>" class="list-group-item list-group-item-action d-flex align-items-center">
                                <img src="uploaded_image/<?= htmlspecialchars($landholder['profile_picture']) ?>" alt="<?= htmlspecialchars($landholder['full_name']) ?>'s Profile Picture" class="profile-img">
                                <span><?= htmlspecialchars($landholder['username']) ?></span>
                                <span class="text-muted ms-auto text-sm"><?= $last_chat_datetime ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>


                    <!-- Tailwind Modal -->
                    <div id="allLandholdersModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            <!-- This element is to trick the browser into centering the modal contents. -->
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <!-- Heroicon name: outline/exclamation -->
                                            <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6-7h12a2 2 0 012 2v8a2 2 0 01-2 2H6a2 2 0 01-2-2v-8a2 2 0 012-2z" />
                                            </svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modalTitle">
                                                All Landholders
                                            </h3>
                                            <div class="mt-2">
                                                <!-- List of all landholders -->
                                                <div class="list-group" id="allLandholdersList">
                                                    <?php foreach ($all_landholders as $landholder) : ?>
                                                        <a href="?receiver_id=<?= $landholder['landholder_id'] ?>" class="list-group-item list-group-item-action">
                                                            <img src="uploaded_image/<?= htmlspecialchars($landholder['profile_picture']) ?>" alt="<?= htmlspecialchars($landholder['full_name']) ?>'s Profile Picture" class="profile-img">
                                                            <span><?= htmlspecialchars($landholder['username']) ?></span>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
                // Function to toggle visibility of all landholders list
                document.getElementById('showAllLandholders').addEventListener('click', function() {
                    // Toggle the display of the modal overlay
                    document.getElementById('allLandholdersModal').classList.toggle('hidden');
                });

                // Close the modal when clicking outside
                document.addEventListener('click', function(event) {
                    var modal = document.getElementById('allLandholdersModal');
                    if (!event.target.closest('.modal-content') && !event.target.closest('#showAllLandholders')) {
                        modal.classList.add('hidden');
                    }
                });
            </script>


            <div class="col-span-8">
                <!-- Chat box content remains the same -->
                <?php if (isset($_GET['receiver_id'])) : ?>
                    <?php
                    // Fetch receiver details
                    try {
                        $stmt = $conn->prepare("SELECT * FROM landholders_tb WHERE landholder_id = ?");
                        $stmt->execute([$_GET['receiver_id']]);
                        $receiver_details = $stmt->fetch(PDO::FETCH_ASSOC);
                    } catch (Exception $e) {
                        die('Error fetching receiver details: ' . $e->getMessage());
                    }
                    ?>


                    <div class="bg-white rounded shadow-md p-6 border border-gray-500 my-8">
                        <div class="user-info mb-3">
                            <img src="uploaded_image/<?= htmlspecialchars($receiver_details['profile_picture']) ?>" alt="<?= htmlspecialchars($receiver_details['full_name']) ?>'s Profile Picture">
                            <h4><?= htmlspecialchars($receiver_details['full_name']) ?></h4>
                        </div>
                        <div class="chat-container">
                            <?php foreach ($messages as $message) : ?>
                                <div class="chat-message <?= $message['sender_type'] === 'user' ? 'landholder' : 'user' ?>">
                                    <div class="chat-bubble <?= $message['sender_type'] === 'user' ? 'landholder' : 'user' ?>">
                                        <?= htmlspecialchars($message["message_text"]) ?>
                                    </div>
                                    <div class="timestamp"><?= date("g:i A", strtotime($message["timestamp"])) ?></div>
                                </div>
                            <?php endforeach; ?>
                            <?php if (empty($messages)) : ?>
                                <p class="text-center mt-5">No messages yet.</p>
                            <?php endif; ?>
                        </div>
                        <form method="POST" class="mt-3 flex">
                            <input type="hidden" name="receiver_id" value="<?= $_GET['receiver_id'] ?>">
                            <textarea name="message_text" class="form-control flex-grow me-2" placeholder="Write your message here..." required></textarea>
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </form>
                    </div>
                    <?php else : ?>
                    <div class="bg-white rounded shadow-sm p-3 border border-gray-200  my-8">
                        <div class="user-info mb-3">
                            <h4>Welcome to chat</h4>
                        </div>
                        <div class="chat-container h-full ">
                            <p class="text-center mt-5">Find landholders and start talking</p>
                            <img src="images/empty1.png" alt="Empty Illustration" class="mx-auto mt-4" style="max-width: 500px;">
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

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

    <!-- Bootstrap and other scripts remain the same -->
    <?php include 'user/user-footer.php' ?>
</body>

</html>