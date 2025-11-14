<aside class="sidebar">
    <div class="sidebar-header">
        <h3>Grading System</h3>
    </div>

    <!-- Main Navigation Links -->
    <nav class="sidebar-nav">
        <ul>
            <li><a href="index.php">Dashboard</a></li>
            <li><a href="subjects.php">Manage Subjects</a></li>
            <li><a href="students.php">Manage Student List</a></li>
            <li><a href="account.php">Account Settings</a></li> 
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    
</aside>

<script>
    // JavaScript to handle sidebar toggle on mobile
    document.getElementById('sidebar-toggle').addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('active');
    });

    </script>