<?php
session_start();
if (!$_SESSION['login_user']) {
    header("Location:login.php");
}
include "header.php";
include "curd.php";
$curd = new Curd();
$data = $curd->get_tasks();
//echo "<pre>";
//print_r($data);
?>
<div class="container">
    <form method="post" action="curd.php?controller=task">
        <div class="form-group">
            <label for="exampleInputTask">Enter Task</label>
            <input type="text" name="task" class="form-control" id="exampleInputTask" aria-describedby="emailHelp" placeholder="Enter Task">
        </div>
        <button type="submit" class="btn btn-primary">Add Task</button>
    </form>

    <div>

        <table class="table">
            <thead class="thead-light">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Task Name</th>
                    <th scope="col">Task Status</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($data as $temp) {
                    $id = $temp['id'];
                    ?>
                    <tr>
                        <th scope="row">1</th>
                        <td><?php echo $temp['task_name']; ?></td>
                        <td>
                            <a href="curd.php?controller=status&state=<?php echo $temp['task_status']; ?>&id=<?php echo $temp['id']; ?>">
                                <button type="button" class="btn btn-primary"><?php echo $temp['task_status']; ?></button>

                            </a>
                        </td>
                        <td>

                            <a href="curd.php?controller=update&id=<?php echo $id; ?>">
                                <button type="button" class="btn btn-success">Update</button>
                            </a>
                            <a href="curd.php?controller=delete&id=<?php echo $id; ?>">
                                <button type="button" class="btn btn-danger">Delete</button>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php include "footer.php"; ?>
