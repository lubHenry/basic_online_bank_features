//Function used to effect automatic investment deductions
If(!function_exists('autoInvestBot')){
    function autoInvestBot(){
        $db = DB::getInstance();
        //fetch users account details
        $res_fro_Bal = $db->query("SELECT accountNumber,accountBal,userId,countryId from Accounts");
        $res_fro_Ball = $res_fro_Bal->results();
        $resuAcc = json_encode($res_fro_Ball);
        $resultAcc = json_decode($resuAcc,true);
        $keysAcc = array_keys($resultAcc);
        for($h=0;$h<count($resultAcc);$h++){
            $custAcc = $resultAcc[$keysAcc[$h]]["accountNumber"];
            $custAccBal = $resultAcc[$keysAcc[$h]]["accountBal"];
            $custId = $resultAcc[$keysAcc[$h]]["userId"];
            $countryId = $resultAcc[$keysAcc[$h]]["countryId"];

            //fetch all active, undermanagement automatic investments in the system by the user
            $auto_inv_query = $db->query("select amount,duration,investmentDate,instalment_amount,frequency,last_instalment_Date,remaining_instalments,target,transactionId 
                                    from investmentTransactions 
                                       where group_investment=0 and active=1 and underManagement=1 and auto=1 and investmentTransactions.userId = ?",[$custId]);
            $auto_res = $auto_inv_query->results();;
            $resu = json_encode($auto_res);
            $result = json_decode($resu,true);
            $keys = array_keys($result);
            for($i=0;$i<count($result);$i++){
                $_frequency = $result[$keys[$i]]["frequency"];
                $_amount = $result[$keys[$i]]["amount"];
                $_duration = $result[$keys[$i]]["duration"];
                $_inveDate = $result[$keys[$i]]["investmentDate"];
                $_instAmount = $result[$keys[$i]]["instalment_amount"];
                $_instDate = $result[$keys[$i]]["last_instalment_Date"];
                $_remInst = $result[$keys[$i]]["remaining_instalments"];
                $_invTarget= $result[$keys[$i]]["target"];
                $_transId = $result[$keys[$i]]["transactionId"];

                $now = date("Y-m-d");
                $varDate = $_inveDate;
                $varDate2 = $_instDate;
                $investDate=date_create($varDate);
                $lastInstDate = date_create($varDate2);
                $nowd = date_create($now);
                date_add($investDate,date_interval_create_from_date_string($_duration));
                $exitDate = date_format($investDate,"Y-m-d");
                $proceed = 0;

                $new_now = new DateTime($now);
                $new_then = new DateTime($exitDate);
                //determine the due date/period for the next instalment, if satisfied, proceed with deduction preparations
                switch ($_frequency) {
                    case "Daily":
                        date_add($lastInstDate,date_interval_create_from_date_string("24 hours"));
                        $prevDate = date_format($lastInstDate,"Y-m-d");
                        $new_last = date_create($prevDate);
                        //determines the number of days between the current date, and the last instalment date.
                        $diff=date_diff($new_last,$nowd);
                        $days = $diff->format("%a");

                        if(($_remInst>1)&&($days=="0")){
                            $_instAmount = $_instAmount;
                            $proceed = 1;
                        }else if(($_remInst>1)&&($days!="0")){
                            $_instAmount = ((int)$days)*$_instAmount;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days=="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days!="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }
                        break;
                    case "Weekly":
                        date_add($lastInstDate,date_interval_create_from_date_string("7 days"));
                        $prevDate = date_format($lastInstDate,"Y-m-d");
                        $new_last = date_create($prevDate);
                        //determines the number of days between the current date, and the last instalment date.
                        $diff=date_diff($new_last,$nowd);
                        $days = $diff->format("%a");

                        if(($_remInst>1)&&($days=="0")){
                            $_instAmount = $_instAmount;
                            $proceed = 1;
                        }else if(($_remInst>1)&&($days!="0")){
                            $_instAmount = ((int)$days)*$_instAmount;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days=="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days!="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }
                        break;
                    case "Monthly":
                        date_add($lastInstDate,date_interval_create_from_date_string("30 days"));
                        $prevDate = date_format($lastInstDate,"Y-m-d");
                        $new_last = date_create($prevDate);
                        //determines the number of days between the current date, and the last instalment date.
                        $diff=date_diff($new_last,$nowd);
                        $days = $diff->format("%a");

                        if(($_remInst>1)&&($days=="0")){
                            $_instAmount = $_instAmount;
                            $proceed = 1;
                        }else if(($_remInst>1)&&($days!="0")){
                            $_instAmount = ((int)$days)*$_instAmount;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days=="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days!="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }
                        break;
                    case "Quaterly":
                        date_add($lastInstDate,date_interval_create_from_date_string("90 days"));
                        $prevDate = date_format($lastInstDate,"Y-m-d");
                        $new_last = date_create($prevDate);
                        //determines the number of days between the current date, and the last instalment date.
                        $diff=date_diff($new_last,$nowd);
                        $days = $diff->format("%a");

                        if(($_remInst>1)&&($days=="0")){
                            $_instAmount = $_instAmount;
                            $proceed = 1;
                        }else if(($_remInst>1)&&($days!="0")){
                            $_instAmount = ((int)$days)*$_instAmount;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days=="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days!="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }
                        break;
                    case "Semi-annually":
                        date_add($lastInstDate,date_interval_create_from_date_string("180 days"));
                        $prevDate = date_format($lastInstDate,"Y-m-d");
                        $new_last = date_create($prevDate);
                        //determines the number of days between the current date, and the last instalment date.
                        $diff=date_diff($new_last,$nowd);
                        $days = $diff->format("%a");

                        if(($_remInst>1)&&($days=="0")){
                            $_instAmount = $_instAmount;
                            $proceed = 1;
                        }else if(($_remInst>1)&&($days!="0")){
                            $_instAmount = ((int)$days)*$_instAmount;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days=="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days!="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }
                        break;
                    case "Annually":
                        date_add($lastInstDate,date_interval_create_from_date_string("365 days"));
                        $prevDate = date_format($lastInstDate,"Y-m-d");
                        $new_last = date_create($prevDate);
                        //determines the number of days between the current date, and the last instalment date.
                        $diff=date_diff($new_last,$nowd);
                        $days = $diff->format("%a");

                        if(($_remInst>1)&&($days=="0")){
                            $_instAmount = $_instAmount;
                            $proceed = 1;
                        }else if(($_remInst>1)&&($days!="0")){
                            $_instAmount = ((int)$days)*$_instAmount;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days=="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }else if(($_remInst<=1)&&($days!="0")){
                            $_instAmount = $_invTarget;
                            $proceed = 1;
                        }
                        break;
                }

                //$new_days = new DateTime($days);
                if(($new_last<=$new_now)&&($new_now<$new_then)&&($proceed==1)){
                        $narration = $_frequency." investment instalment for " . $_duration;
                        //Effect deductions from sending account to investment account
                        //update account balance for the account sending
                        $finBal = $custAccBal - $_instAmount;
                        updateAccount('accountBal', $custAcc, $finBal);
                        transactionLog($custAcc, $narration, $_instAmount, 'Debit', '1', $_transId);
                        tranSuccess($_transId, $custAcc);
                        //update account balance for the investments account
                        $res_to_Bal = $db->query("SELECT accountNumber,accountBal from Accounts WHERE accountType = 'investment' and countryId = ?", [$countryId]);
                        $res_to_Ball = $res_to_Bal->first();
                        $accNo = $res_to_Ball->accountNumber;
                        $accToBal = $res_to_Ball->accountBal;
                        //update account balance for the investment account
                        $finToBal = $accToBal + $_instAmount;
                        updateAccount('accountBal', $accNo, $finToBal);
                        //increase for the receiving account
                        transactionLog($accNo, $narration, $_instAmount, 'Credit', '1', $_transId);
                        //affirm that transactoon is successful for both accounts
                        tranSuccess($_transId, $accNo);
                        //Calculating remaining target and number of proceeding instalments after the first installment
                        $_amount += $_instAmount;
                        $_invTarget -= $_instAmount;
                        if(((int)$days)>1){
                            $_remInst -= (int)$days;
                        }else{
                            $_remInst -= 1;
                        }
                        $today = date("Y-m-d H-m-s");
                        //Updates the investment transactions table accordingly
                        $db->query("UPDATE investmentTransactions SET 
                                      amount=?,last_instalment_Date=?,target=?,remaining_instalments=? 
                                            where transactionId = ?", [$_amount,$today,$_invTarget,$_remInst,$_transId]);
                }else{}
            }
        }
    }
}
