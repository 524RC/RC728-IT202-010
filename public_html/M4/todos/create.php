<?php
require_once(__DIR__ . "/../../../lib/db.php"); ?>

<?php
// don't edit - this
$expected_fields = ["task", "due", "assigned"];
$diff = array_diff($expected_fields, array_keys($_GET));

if (empty($diff)) {

    // data variables, don't edit
    $task = $_GET["task"];
    $due = $_GET["due"]; //hint: must be a valid MySQL date format
    $assigned = $_GET["assigned"]; // Must be "self" or a valid format (not empty or equivalent)

    $is_valid = true;
    // TODO Validate the incoming data for correct format based on the SQL table definition.
    // When not valid, provide a user-friendly message of what specifically was wrong and set $is_valid to false.
    // Assigned should check for "self" if a valid format/value isn't provided.
    // Start validations
    // can edit here
    //rc728 3/27/26
    if (empty(trim($task))) {
        echo "Task is required.<br>";
        $is_valid = false;
    }

    $date = DateTime::createFromFormat("Y-m-d", $due);
    if (!$date || $date->format("Y-m-d") !== $due) {
        echo "Due date must be a valid date in YYYY-MM-DD format.<br>";
        $is_valid = false;
    }

    $assigned = trim($assigned);

    if (empty($assigned)) {
        echo "Assigned was empty, defaulted to self.<br>";
        $assigned = "self";
    } else {
        if (strtolower($assigned) !== "self") {
            if (!preg_match("/^[a-zA-Z-' ]+$/", $assigned)) {
                echo "Assigned was invalid, defaulted to self.<br>";
                $assigned = "self";
            }
        }
    }
    // End validations

    
    if ($is_valid) {
        /*
        Design a query to insert the incoming data to the proper columns.
        Ensure valid and proper PDO named placeholders are used.
        https://phpdelusions.net/pdo
        */
        /* rc728 3/29/26 */
        $query = "INSERT INTO todos (task, due, assigned) VALUES (:task, :due, :assigned)"; // edit this
        $params = [
            ":task" => $task,
            ":due" => $due,
            ":assigned" => $assigned
        ]; // Apply the proper PDO placeholder to variable mapping here
        try {
            $db = getDB();  
            $stmt = $db->prepare($query);
            $r = $stmt->execute($params);
            if ($r) {
                echo "Inserted new Todo with id " . $db->lastInsertId();
            } else {
                echo "Failed to insert";
            }
        } catch (PDOException $e) {
            // extra credit
            // check if the exception was related to a unique constraint
            // provide an appropriate user-friendly message for this scenario
            // Otherwise show the default message below
            echo "There was an error inserting the record; check the logs (terminal)";
            error_log("Insert Error: " . var_export($e, true)); // shows in the terminal
        }
    } else {
        error_log("Creation input wasn't valid");
    }
}
?>
<html>

<body>
    <?php require_once(__DIR__ . "/../nav.php"); ?>
    <section>
        <h2>Create ToDo </h2>
        <!-- rc728 3/27/26 --> 
        <form>
            <!-- design the form with proper labels and input fields with the correct types based on the SQL table.
             Wrap each label/input pair in a div tag.
             For "Assigned" ensure the default value is "self". -->
          
            
            <div>
                <label for="task">Task</label>
                <input id="task" name="task" type="text" required />
            </div>

            <div>
                <label for="due">Due Date</label>
                <input id="due" name="due" type="date" required />
            </div>

            <div>
                <label for="assigned">Assigned</label>
                <input id="assigned" name="assigned" type="text" value="self" required />
            </div>

            <div>
                <input type="submit" />
            </div>
        </form>
    </section>
</body>

</html>