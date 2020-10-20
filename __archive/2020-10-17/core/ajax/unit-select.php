<?php

require '../php/_autoload.php';
require '../model/_autoload.php';

// JSON Response
$response = array(
    "success" => 'success',
    "content" => array(
        "modal" => ''
    ),
    "contentType" => 'modal'
);

if (isset($_POST['unitId'])) {
    $unitId = new form_validation($_POST['unitId'], 'str-int', 'Unit ID', true);

    $unit = '';
    if ($unitId -> valid == 1) {
        // Verify if the Employee ID is valid
        if ($unitId -> value == 'new-rec') {
            $unit = array(
                "description" => '',
                "FK_mscDepartment" => ''
            );
        } else {
            $unit = MscUnit::show($unitId -> value);
            // die(var_dump($employee));
            if (is_null($unit)) {
                $unitId -> valid = 0;
                $unitId -> err_msg = "Department Record not found";
            }
        }
    }

    if ($unitId -> valid == 1) {
        $departmentElem = "";
        $department = MscDepartment::index();
        $departmentElem .= "
            <select name='departmentId' id='' class='form-control'>
                <option value='' style='display:none;'>Select Department</option>
        ";
        if (is_array($department)) {
            if (count($department) > 0) {
                foreach($department as $row) {
                    $selected = ($row['PK_mscdepartment'] == $unit['FK_mscDepartment']) 
                        ? 'selected' 
                        : '';
                    $departmentElem .= "<option value='{$row['PK_mscdepartment']}' {$selected}>{$row['description']}</option>";
                }
            }
        }
        $departmentElem .= "</select>";
        $response['content']['modal'] = modalize(
            '<div class="row">
                <div class="col-sm-12">
                <h2 class="header capitalize text-center">Unit Management</h2>
                <p class="para-text text-center">Please fill the field with a valid information to continue.</p>
                </div>
                
                <div class="col-sm-12 item-guide-mgmt">
                    <form form-name="division-form" action="../core/ajax/unit-manage.php" tran-type="async-form">
                        <input type="text" name="unitId" hidden="hidden" value="' . $unitId -> value . '">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Unit Name: </label>
                                    <div class="form-group col-sm-12">
                                        <input type="text" class="form-control" name="description" placeholder="Unit Name" value="' . $unit['description'] . '">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <label for="" class="text-left control-label col-sm-12">Department: </label>
                                    <div class="form-group col-sm-12">
                                        ' . $departmentElem . '
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>', 
            array(
                "trasnType" => 'regular',
                "btnLbl" => 'Submit'
            )
        );
    } else {
        $response['success'] = 'failed';
        $response['contentType'] = 'modal';
        $response['content']['modal'] = modalize(
            "<div class='row text-center'>
                <h2 class='header capitalize col-12'>System Error Encountered</h2>
                <p class='para-text col-12'>Error Details: {$unitId -> err_msg}</p>
            </div>", 
            array(
                "trasnType" => 'error',
                "btnLbl" => 'Dismiss'
            )
        );
    }

} else {
    $response['success'] = 'failed';
    $response['contentType'] = 'modal';
    $response['content']['modal'] = modalize(
        '<div class="row text-center">
            <h2 class="header capitalize col-12">System Error Encountered</h2>
            <p class="para-text col-12">Error Details: Insufficient Data Submitted<br/> Please contact your System Administrator</p>
        </div>', 
        array(
            "trasnType" => 'error',
            "btnLbl" => 'Dismiss'
        )
    );   
}


// Encode JSON Response
encode_json_file($response);