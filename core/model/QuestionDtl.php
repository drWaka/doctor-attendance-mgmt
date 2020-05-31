<?php

class QuestionDtl {

    public static function index() {
        
    }

    public static function create() {

    }

    public static function show() {

    }

    public static function delete () {

    }

    public static function update() {

    }

    public static function getByQuestionMstr($questionMstrId) {
        // Insert Reponse Records
        $query = "
            SELECT a.* 
            FROM questiondtl AS a
            INNER JOIN questiongrp AS b ON a.FK_questionGrp = b.PK_questionGrp
            WHERE a.FK_questionMstr = '{$questionMstrId}'
                AND a.isDeleted = 0
            ORDER BY b.sorting, a.sorting
        ";
        $result = $GLOBALS['connection'] -> query($query);

        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];

    }

    public static function getByPage($details) {
        $query = "
            SELECT a.* 
            FROM questiondtl AS a
            INNER JOIN questiongrp AS b ON a.FK_questionGrp = b.PK_questionGrp
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND b.sorting = '{$details['groupNo']}'
                AND a.sorting = '{$details['pageNo']}'
                AND a.isDeleted = 0 
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            return $result -> fetch_all(MYSQLI_ASSOC);
        }

        return [];
    }

    public static function getQuestionDetailId($details) {
        $query = "
            SELECT a.PK_questionDtl 
            FROM questiondtl AS a
            INNER JOIN questiongrp AS b ON a.FK_questionGrp = b.PK_questionGrp 
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND b.sorting = '{$details['groupNo']}'
                AND a.sorting = '{$details['pageNo']}'
                AND a.isDeleted = 0 
            LIMIT 1
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['PK_questionDtl'];
        }

        return 0;
    }

    public static function getMaxPageNo($details) {
        $query = "
            SELECT a.sorting 
            FROM questiondtl AS a
            INNER JOIN questiongrp AS b ON a.FK_questionGrp = b.PK_questionGrp 
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND b.sorting = '{$details['groupNo']}'
                AND a.isDeleted = 0 
            ORDER BY b.sorting DESC, a.sorting DESC LIMIT 1
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['sorting'];
        }

        return 0;
    }

    public static function getQuestionType($details) {
        $query = "
            SELECT a.FK_questionType 
            FROM questiondtl AS a
            INNER JOIN questiongrp AS b ON a.FK_questionGrp = b.PK_questionGrp 
            WHERE a.FK_questionMstr = '{$details['questionMstrId']}'
                AND b.sorting = '{$details['groupNo']}'
                AND a.sorting = '{$details['pageNo']}'
                AND a.isDeleted = 0 
            LIMIT 1
        ";
        $result = $GLOBALS['connection'] -> query($query);
        if ($result -> num_rows > 0) {
            $record =  $result -> fetch_all(MYSQLI_ASSOC);
            return $record[0]['FK_questionType'];
        }

        return '';
    }

    public static function getResponseField($details) {
        // ToDo :  Make the Field Values Dynamic
        $fieldType = self::getQuestionType($details);
        $sessionId = QuestionSession::getSessionByEmpDate(array(
            "questionMstrId" => $details['questionMstrId'],
            "employeeId" => $details['employeeId'],
            "sessionDate" => date('Y-m-d')
        ));
        $questionDtlId = self::getQuestionDetailId(array(
            "questionMstrId" => $details['questionMstrId'],
            "groupNo" => $details['groupNo'],
            "pageNo" => $details['pageNo']
        ));
        $questionResponse = QuestionResponse::getResponseByNo(array(
            "questionMstrId" => $details['questionMstrId'], 
            "emplyeeId" => $details['employeeId'], 
            "questionDtlId" => $questionDtlId, 
            "questionSessionId" => $sessionId
        ));
        // Error Handling
        $error = array(
            "class" => '',
            "icon" => '',
            "text" => ''
        );
        if ($details['error']['hasError'] == 1) {
            $error["class"] = 'is-invalid';
            $error["icon"] = '<span class="fa fa-times invalid-icon"></span>';
            $error["text"] = '<div class="invalid-feedback">' . $details['error']['errorMessage'] . '</div>';
        }

        $field = "";
        if ($fieldType == 'OPTN') {
            $field = "
                <label class='form-check-label col-sm-10 offset-sm-1' for='yes'>
                    <input class='form-check-input form-control' type='radio' name='response[]' id='yes' value='yes'> Yes
                </label>

                <label class='form-check-label col-sm-10 offset-sm-1' for='no'>
                    <input class='form-check-input form-control' type='radio' name='response[]' id='no' value='no'> No
                </label>
            ";
        } else if ($fieldType == 'DRP_DWN') {
            $selected = array(
                "yes" => '',
                "no" => ''
            );
            if (strtolower($questionResponse) == 'yes') {
                $selected['yes'] = 'selected';
            } else if (strtolower($questionResponse) == 'no') {
                $selected['no'] = 'selected';
            }

            $field = "
                <input type='text' name='dataType' hidden='hidden' value='str'/>
                <input type='text' name='isRequired' hidden='hidden' value='1'/>
                <input type='text' name='desc' hidden='hidden' value='Response'/>

                <div class='col-10 offset-1'>
                    <select name='response' class='form-control {$error['class']}' id='' field-desc='Response' field-type='text' field-required='1'>
                        <option value='' selected>Select Response</option>
                        <option value='yes' {$selected['yes']}>Yes, I did</option>
                        <option value='no' {$selected['no']}>No, I didn't</option>
                    </select>
                    {$error['icon']}
                    {$error['text']}
                </div>
            ";
        } else if ($fieldType == 'DATE') {
            $field = "
                <input type='text' name='dataType' hidden='hidden' value='date'/>
                <input type='text' name='isRequired' hidden='hidden' value='1'/>
                <input type='text' name='desc' hidden='hidden' value='Symptoms onset date'/>

                <div class='col-10 offset-1'>
                    <input 
                        type='date' 
                        name='response' 
                        class='form-control {$error['class']}'
                        field-desc='Symptoms onset date' 
                        field-type='date' 
                        field-required='1'
                        value='{$questionResponse}'
                    >
                    {$error['icon']}
                    {$error['text']}
                </div>
            ";
        }

        return $field;
    }

    public static function getFormSection($formDetails) {

        $max = array();
        $max["group"] = QuestionGrp::getMaxGroupNo($formDetails['questionMstrId']);
        $max["page"] = self::getMaxPageNo(array(
            "questionMstrId" => $formDetails['questionMstrId'],
            "groupNo" => $max["group"]
        ));

        // Previous Question Details
        $prev = array(
            "groupNo" => $formDetails['groupNo'],
            "pageNo" => (intval($formDetails['pageNo']) - 1)
        );

        if ($prev['pageNo'] == 0) {
            if ($prev['groupNo'] !== 1) {
                $prev['groupNo'] = (intval($prev['groupNo']) - 1);
                $prev['pageNo'] = self::getMaxPageNo(array(
                    "questionMstrId" => $formDetails['questionMstrId'],
                    "groupNo" => $prev['groupNo']
                ));
            } else {
                $prev['groupNo'] = 1;
                $prev['pageNo'] = 1;
            }
        }
        
        $navigation = "";
        if ($formDetails['pageNo'] == 1 && $formDetails['groupNo'] == 1) {
            $navigation = "
                <div class='form-row'>
                    <div class='col-4 offset-4 text-center'>
                        <button type='button' class='btn btn-info w-100 form-submit-button'>Next</button>
                    </div>
                </div>
            ";
        } else if ($formDetails['pageNo'] == $max['page'] &&  $formDetails['groupNo'] == $max['group']) {
            $navigation = "
                <div class='form-row'>
                    <div class='col-4 offset-2 text-center'>
                        <button type='button' class='btn btn-info w-100 transaction-button'
                            tran-type='async-form'
                            tran-link='core/ajax/survey-question-back.php'
                            tran-data='{
                                &quot;questionMstrId&quot; : &quot;{$formDetails['questionMstrId']}&quot;,
                                &quot;sessionId&quot; : &quot;{$formDetails['questionSessionId']}&quot;,
                                &quot;employeeId&quot; : &quot;{$formDetails['employeeId']}&quot;,
                                &quot;pageNo&quot; : &quot;{$prev['pageNo']}&quot;,
                                &quot;groupNo&quot; : &quot;{$prev['groupNo']}&quot;
                            }'
                            tran-container='dynamic-content'
                        >Back</button>
                    </div>
                    <div class='col-4 text-center'>
                        <button type='button' class='btn btn-info w-100 form-submit-button'>Finish</button>
                    </div>
                </div>
            ";
        } else {
            $navigation = "
                <div class='form-row'>
                    <div class='col-4 offset-2 text-center'>
                        <button type='button' class='btn btn-info w-100 transaction-button'
                            tran-type='async-form'
                            tran-link='core/ajax/survey-question-back.php'
                            tran-data='{
                                &quot;questionMstrId&quot; : &quot;{$formDetails['questionMstrId']}&quot;,
                                &quot;sessionId&quot; : &quot;{$formDetails['questionSessionId']}&quot;,
                                &quot;employeeId&quot; : &quot;{$formDetails['employeeId']}&quot;,
                                &quot;pageNo&quot; : &quot;{$prev['pageNo']}&quot;,
                                &quot;groupNo&quot; : &quot;{$prev['groupNo']}&quot;
                            }'
                            tran-container='dynamic-content'
                        >Back</button>
                    </div>
                    <div class='col-4 text-center'>
                        <button type='button' class='btn btn-info w-100 form-submit-button'>Next</button>
                    </div>
                </div>
            ";
        }

        $responses = self::getResponseField(array(
            "questionMstrId" => $formDetails['questionMstrId'],
            "employeeId" => $formDetails['employeeId'],
            "pageNo" => $formDetails['pageNo'],
            "groupNo" => $formDetails['groupNo'],
            "error" => $formDetails['error']
        ));

        return "
        <form class='margin-top-sm transaction-form' 
            action='core/ajax/survey-question-manage.php'
            method='POST'
            tran-type='async-form'
            tran-container='dynamic-content'
            form-name='survey-form'
            submit-type='asynchronous'
        >
            <input type='text' name='questionMstrId' hidden='hidden' value='{$formDetails['questionMstrId']}'/>
            <input type='text' name='sessionId' hidden='hidden' value='{$formDetails['questionSessionId']}'/>
            <input type='text' name='employeeId' hidden='hidden' value='{$formDetails['employeeId']}'/>
            <input type='text' name='pageNo' hidden='hidden' value='{$formDetails['pageNo']}'/>
            <input type='text' name='groupNo' hidden='hidden' value='{$formDetails['groupNo']}'/>

            <div class='col-10 offset-1 margin-top-md margin-bottom-xs' style='font-weight: 600'>
                Response :
            </div>
            <div class='form-row'>
                {$responses}
            </div>

            {$navigation}
        </form>
        ";
    }
}