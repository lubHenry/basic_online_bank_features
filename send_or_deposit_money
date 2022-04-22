//Function that sends deposit to another account and records commission depending on the range/band in which the client's money falls.
if(!function_exists('send')){
        function send($froAccountNumber,$country=null,$bank=null,$branch=null,$toAccountNumber,$toExternalAccount,$amount,$narrat,$txnId){
            $db = DB::getInstance();
            $res_fro_Bal = $db->query("SELECT accountBal,userId,currency,countryId from Accounts WHERE accountNumber = ?",[$froAccountNumber]);
            $res_fro_Ball = $res_fro_Bal->first();
            $accBal = $res_fro_Ball->accountBal;
            $userId = $res_fro_Ball->userId;
            $countryId = $res_fro_Ball->countryId;
            $currency = $res_fro_Ball->currency;
            $nart = "Sent to ".$toAccountNumber.$toExternalAccount." - ".$narrat;
            $narcive = "From ".$froAccountNumber." - ".$narrat;
            //select conversion rate for the customer.
            $querRate = $db->query("SELECT count(*) as numb,SUM(cubRate) as rate from accountsHistory where accountNumber=? and successful=1",[$froAccountNumber])->first();
            $exnRate = ($querRate->rate)/($querRate->numb);
            //update account balance for the account sending
            $finBal = $accBal - $amount;
            updateAccount('accountBal',$froAccountNumber,$finBal);
            //deducting from sending account number
            transactionLog($froAccountNumber,$nart,$amount,'Debit',$exnRate,$txnId);
            //update account balance for the account receiving
            $res_to_Bal = $db->query("SELECT accountBal from Accounts WHERE accountNumber = ?",[$toAccountNumber]);
            $res_to_Ball = $res_to_Bal->first();
            $accToBal = $res_to_Ball->accountBal;
            //update account balance for the account sending
            $finToBal = $accToBal + $amount;
            updateAccount('accountBal',$toAccountNumber,$finToBal);
            //increase for the receiving account
            transactionLog($toAccountNumber,$narcive,$amount,'Credit',$exnRate,$txnId);
            //affirm that transactoon is successful for both accounts
            tranSuccess($txnId,$froAccountNumber);
            tranSuccess($txnId,$toAccountNumber);
            //record send transaction
            //Choose the country Id of the receipient basing on the selected countryName option
            $res_country = $db->query("SELECT id from countries WHERE countryName = ?",[$country])->first();
            $r_countryId = $res_country->id;
                //inserts send transaction into sendTransactions table
            $Tdate = date('Y-m-d H:i:s');
            $db->query("INSERT INTO sendTransactions(sourceAccount,countryId,bankName,bankBranchName, receiptAccount,extAccount, narration, amount,sentCurrency,transactionId, sendDate)
                        VALUES (?,?,?,?,?,?,?,?,?,?,?)", [$froAccountNumber,$r_countryId,$bank,$branch,$toAccountNumber,$toExternalAccount,$narrat,$amount,$currency,$txnId,$Tdate]);
            //implement transaction commission deduction
            //select charges
            $charge = $db->query('select transactionCharges.lowerBand, transactionCharges.midBand,transactionCharges.upperBand
                                    from transactionCharges join Accounts join countries join users
                                    where Accounts.userId=users.id and users.country=countries.id
                                      and countries.id=transactionCharges.countryId and users.id=?',[$userId]);
            $charges = $charge->first();
            $lowerBand = $charges->lowerBand;
            $midBand = $charges->midBand;
            $upperBand = $charges->upperBand;
            //select transaction limits
            $limit = $db->query('select Accounts.userId,transactionLimits.lowerBand, transactionLimits.midBand
                                    from transactionLimits join Accounts join countries join users
                                    where Accounts.userId=users.id and users.country=countries.id
                                      and countries.id=transactionLimits.countryId and users.id=?',[$userId]);
            $limits = $limit->first();
            $lowerLimit = $limits->lowerBand;
            $midLimit = $limits->midBand;
            $com_re = $db->query("select distinct Accounts.accountNumber from countries inner join Accounts
                                                on countries.id=Accounts.countryId
                                    where Accounts.accountType = ? and Accounts.countryId = ? ",['commission',$countryId]);
            $com_res = $com_re->first();
            $comAcc = $com_res->accountNumber;
            //Determine the charge per transaction amount and record it
            if ($amount<=$lowerLimit){
                $chargeRate = $lowerBand*$amount;
                $narrt = "Sending charge to ".$toAccountNumber.$toExternalAccount." - ".$narrat;
                $narcive = "Sending Charge From ".$froAccountNumber." - ".$narrat;
                //record deducted commission from sending account
                transactionLog($froAccountNumber,$narrt,$chargeRate,'Debit',$exnRate,$txnId);
                //record commission amount
                transactionLog($comAcc,$narcive,$chargeRate,'Credit',$exnRate,$txnId);
                //affirm that commission has been deducted
                tranSuccess($txnId,$comAcc);
                tranSuccess($txnId,$froAccountNumber);
                $chr_fro_Bal = $db->query("SELECT accountBal from Accounts WHERE accountNumber = ?",[$froAccountNumber]);
                $chr_fro_Ball = $chr_fro_Bal->first();
                $ChrAccBal = $chr_fro_Ball->accountBal;
                //update account balance for the charged sending account
                $ChrFinBal = $ChrAccBal - $chargeRate;
                updateAccount('accountBal',$froAccountNumber,$ChrFinBal);
                }elseif (($amount>$lowerLimit)&&($amount<=$midLimit)){
                    $chargeRate = $midBand*$amount;
                    $narrt = "Sending charge to ".$toAccountNumber.$toExternalAccount." - ".$narrat;
                    $narcive = "Sending Charge From ".$froAccountNumber." - ".$narrat;
                    //record amount deductes from sending account
                    transactionLog($froAccountNumber,$narrt,$chargeRate,'Debit',$exnRate,$txnId);
                    //record commission amount
                    transactionLog($comAcc,$narcive,$chargeRate,'Credit',$exnRate,$txnId);
                    //affirm that commission has been deducted
                    tranSuccess($txnId,$comAcc);
                    tranSuccess($txnId,$froAccountNumber);
                    $chr_fro_Bal = $db->query("SELECT accountBal from Accounts WHERE accountNumber = ?",[$froAccountNumber]);
                    $chr_fro_Ball = $chr_fro_Bal->first();
                    $ChrAccBal = $chr_fro_Ball->accountBal;
                    //update account balance for the charged sending account
                    $ChrFinBal = $ChrAccBal - $chargeRate;
                    updateAccount('accountBal',$froAccountNumber,$ChrFinBal);
                }else{
                    $chargeRate = $upperBand*$amount;
                    $narrt = "Sending charge to ".$toAccountNumber.$toExternalAccount." - ".$narrat;
                    $narcive = "Sending Charge From ".$froAccountNumber." - ".$narrat;
                    //record deducted amount from sending account
                    transactionLog($froAccountNumber,$narrt,$chargeRate,'Debit',$exnRate,$txnId);
                    //record commission amount
                    transactionLog($comAcc,$narcive,$chargeRate,'Credit',$exnRate,$txnId);
                    //affirm that commission has been deducted
                    tranSuccess($txnId,$comAcc);
                    tranSuccess($txnId,$froAccountNumber);
                    $chr_fro_Bal = $db->query("SELECT accountBal from Accounts WHERE accountNumber = ?",[$froAccountNumber]);
                    $chr_fro_Ball = $chr_fro_Bal->first();
                    $ChrAccBal = $chr_fro_Ball->accountBal;
                    //update account balance for the charged sending account
                    $ChrFinBal = $ChrAccBal - $chargeRate;
                    updateAccount('accountBal',$froAccountNumber,$ChrFinBal);
                }
        }

}
