<?php

include '../components/connection.php';

session_start();


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

<div class="container mx-auto overflow-y-auto py-8 px-6">
    <h1 class="text-2xl font-bold mb-6 text-center">Post An Announcement</h1>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Post Announcement</h3>
        </div>

        <form action="process_create_announcement.php" method="post">
            <div class=" my-4 mx-4">
                <label for="type" class="block text-md font-medium text-gray-700">Choose Type: </label>
                <select id="type" name="type" class="mt-2 bg-white block w-full border border-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-14 px-3">
                    <option value="updates">Updates</option>
                    <option value="blogs">Blogs</option>
                    <option value="others">Others</option>
                </select>
            </div>
            <div class="my-4 mx-4">
                <label for="title" class="block text-md font-medium text-gray-700">Title: </label>
                <input type="text" id="title" name="title" class="mt-2 bg-white block w-full border border-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-14 px-3" placeholder="Input title">
            </div>
            <div class="my-4 mx-4">
                <label for="details" class="block text-sm font-medium text-gray-700">Details</label>
                <textarea id="details" name="details" rows="4" class="mt-2 bg-white block w-full border border-gray-400 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm h-36 px-3"></textarea>
            </div>
            <button type="submit" class="bg-indigo-500 text-white px-4 py-2 rounded-md hover:bg-indigo-600  my-4 mx-4">Create Announcement</button>
        </form>
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