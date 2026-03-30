<?php
require(__DIR__ . "/base.php");
$a1 = [
    ["id" => 1, "make" => "Toyota", "model" => "Camry", "year" => 2010],
    ["id" => 2, "make" => "Honda", "model" => "Civic", "year" => 2005]
];

$a2 = [
    ["id" => 3, "make" => "Ford", "model" => "Mustang", "year" => 1995],
    ["id" => 4, "make" => "Chevrolet", "model" => "Impala", "year" => 2000]
];

$a3 = [
    ["id" => 5, "make" => "Nissan", "model" => "Altima", "year" => 2015],
    ["id" => 6, "make" => "BMW", "model" => "3 Series", "year" => 2018]
];

$a4 = [
    ["id" => 7, "make" => "Mercedes", "model" => "C Class", "year" => 2011],
    ["id" => 8, "make" => "Audi", "model" => "A4", "year" => 1990]
];

function processCars($cars) {
    printProblemData($cars);
    echo "<br>New Properties Output:<br>";
    
    // Note: use the $cars variable to iterate over, don't directly touch $a1-$a4
    // TODO Objective: Add logic to create a new array ($processedCars) with original properties plus age and isClassic. isClassic is a boolean based on today\'s year and the $classic_age variable.
    $currentYear = null; // determine current year
    $processedCars = []; // result array
    $classic_age = 25; // don't change this value
    // Start edits
    // Rc728 3/30/26
    // step 1: make $currentYear = 2026
    // step 2: loop through the $cars array to see each individual car
    // step 3: calculate the age by subtracting $currentYear by $car["year"] which would subtract the today's year by the year the car was made
    // step 4: determine if the car is classic by seeing if the car age is greater than or equal to $classic_age
    // step 5: list out the original properties along with age and isClassic
    $currentYear = 2026;

    foreach ($cars as $car) {
        $age = $currentYear - $car["year"];
        $isClassic = $age >= $classic_age;

        $processedCars[] = [
            "id" => $car["id"],
            "make" => $car["make"],
            "model" => $car["model"],
            "year" => $car["year"],
            "age" => $age,
            "isClassic" => $isClassic
        ];
    }
    // End edits
    echo "<pre>" . var_export($processedCars, true) . "</pre>";
    
}
$ucid = "Rc728"; // replace with your UCID
printHeader($ucid, 2); 
?>
<table>
    <thead>
        <tr>
            <th>A1</th>
            <th>A2</th>
            <th>A3</th>
            <th>A4</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>
                <?php processCars($a1); ?>
            </td>
            <td>
                <?php processCars($a2); ?>
            </td>
            <td>
                <?php processCars($a3); ?>
            </td>
            <td>
                <?php processCars($a4); ?>
            </td>
        </tr>
    </tbody>
</table>
<?php printFooter($ucid,2); ?>
<style>
    table {
        border-spacing: 1em 3em;
        border-collapse: separate;
    }

    td {
        border-right: solid 1px black;
        border-left: solid 1px black;
    }
</style>