<?php

include '../components/connection.php';

session_start();
// Pagination settings
$limit = 7; // Messages per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Current page

// Calculate offset for pagination
$offset = ($page - 1) * $limit;

// Fetch messages from contacts_tb with pagination
$sql = "SELECT id, name, email, message, created_at FROM contacts_tb ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count total messages
$totalMessages = $conn->query("SELECT COUNT(*) FROM contacts_tb")->fetchColumn();

// Calculate total pages
$totalPages = ceil($totalMessages / $limit);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tailwind Admin Template</title>
    <meta name="author" content="David Grzyb">
    <meta name="description" content="">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.19/tailwind.min.css" rel="stylesheet">
    <!-- Include jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

</head>

<body class="bg-gray-100 font-family-karla flex">

<?php include 'admin-header.php' ?>

<!-- Main Content Section -->
<div class="container mx-auto overflow-y-auto py-8 px-6">
        <h1 class="text-2xl font-bold mb-6 text-center">View Contact Messages</h1>

        <!-- Messages Container -->
        <div class="bg-blue-100 shadow overflow-hidden sm:rounded-lg">
            <!-- Header -->
            <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Contact Messages</h3>
            </div>

            <!-- Messages -->
            <div class="p-4 space-y-4">
                <!-- Display fetched messages -->
                <?php foreach ($messages as $row) : ?>
                    <div class="border border-gray-200 p-4 rounded-lg bg-white shadow-sm">
                        <div class="flex justify-between items-center mb-2 cursor-pointer" data-bs-toggle="modal" data-bs-target="#messageModal<?= $row['id'] ?>">
                            <div>
                                <h4 class="text-lg font-semibold"><?= htmlspecialchars($row['name']) ?></h4>
                                <p class="text-sm text-gray-500"><?= htmlspecialchars($row['email']) ?></p>
                            </div>
                        </div>
                        <p class="mb-2 text-gray-700"><?= nl2br(htmlspecialchars($row['message'])) ?></p>
                        <div class="text-right text-sm text-gray-400">
                            <?= htmlspecialchars($row['created_at']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Display message if no records found -->
                <?php if (empty($messages)) : ?>
                    <div class="border border-gray-200 p-4 rounded-lg bg-white shadow-sm text-center">
                        <p class="text-sm text-gray-500">No messages found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-4 flex justify-center">
            <?php if ($totalPages > 1) : ?>
                <?php if ($page > 1) : ?>
                    <a href="?page=<?= ($page - 1) ?>" class="mx-1 px-3 py-1 bg-gray-200 text-gray-700 rounded-md">
                        <i class="fas fa-arrow-left"></i> Previous
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
                    <a href="?page=<?= $i ?>" class="mx-1 px-3 py-1 <?= $i === $page ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700' ?> rounded-md">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages) : ?>
                    <a href="?page=<?= ($page + 1) ?>" class="mx-1 px-3 py-1 bg-gray-200 text-gray-700 rounded-md">
                        Next <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal -->
<div class="modal fade" id="messageModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="messageModalLabel<?= $row['id'] ?>" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="messageModalLabel<?= $row['id'] ?>">Message from <?= htmlspecialchars($row['name']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p><?= nl2br(htmlspecialchars($row['message'])) ?></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <!-- Add Reply Button -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal<?= $row['id'] ?>">
          Reply
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="replyModalLabel<?= $row['id'] ?>" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="replyModalLabel<?= $row['id'] ?>">Reply to <?= htmlspecialchars($row['name']) ?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="post" action="send_reply.php">
        <div class="modal-body">
          <div class="mb-3">
            <label for="replyMessage<?= $row['id'] ?>" class="form-label">Your Reply</label>
            <textarea class="form-control" id="replyMessage<?= $row['id'] ?>" name="replyMessage" rows="5"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Send</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- AlpineJS -->
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <!-- Font Awesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js" integrity="sha256-KzZiKy0DWYsnwMF+X1DvQngQ2/FxF7MF3Ff72XcpuPs=" crossorigin="anonymous"></script>
    <!-- ChartJS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha256-R4pqcOYV8lt7snxMQO/HSbVCFRPMdrhAFMH+vr9giYI=" crossorigin="anonymous"></script>

</body>

</html>