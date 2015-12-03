<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Table extends CI_Controller
{
    function __construct(){
        parent :: __construct(); 
            $this->load->model('Route_model');
            $this->load->model('Group_model');
            $this->load->model('Order_model');
            $this->load->library('form_validation');    
        // Here you should add some sort of user validation
        // to prevent strangers from pulling your table data
    } 


    //生成给酒店的房间信息表 
    function hotel(){
        //获取本团的tourCode                     
        $t_id = $this->input->get("id",true);

        //将旅游团id传给房间列表函数   
        $query = $this -> room_list($t_id);

 
        if (!$query)
            return false; 
        // Starting the PHPExcel library
        $this -> load -> library('PHPExcel');
        $this -> load -> library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel -> getProperties() -> setCreator("Pacific Delight Ltd")-> setTitle("Room List for Hotel");
 
        $objPHPExcel -> setActiveSheetIndex(0); 
        $objSheet=$objPHPExcel->getActiveSheet();//获取当前活动sheet
        // Field names in the first row

        //本旅游团的基础信息
        $objSheet->setCellValue('B1', 'Tour Code')->setCellValue('C1', $query['tour_code']);
        $objSheet->setCellValue('B2', 'Room:')->setCellValue('C2', $query['room_request']);
        $objSheet->setCellValue('B3', 'Pax:')->setCellValue('C3', $query['room_people']);
        $objSheet->setCellValue('B4', 'Child:')->setCellValue('C4', $query['child_detail']);

        //本旅游团各订单的信息
        $objSheet->setCellValue('B6', 'ID')->setCellValue('C6', 'Room Type')->setCellValue('E6', 'Customers');

        $j=7;
            foreach($query['room_list'] as $key=>$val){                    
                    $objSheet->getStyle('B'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('B'.$j)->getFill()->getStartColor()->setARGB('438eb9');
                    $objSheet->getStyle('C'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('C'.$j)->getFill()->getStartColor()->setARGB('438eb9');
                    $objSheet->getStyle('D'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('D'.$j)->getFill()->getStartColor()->setARGB('438eb9');
                    $objSheet->getStyle('E'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('E'.$j)->getFill()->getStartColor()->setARGB('438eb9');
                    $objSheet->getStyle('F'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('F'.$j)->getFill()->getStartColor()->setARGB('438eb9');
                    $objSheet->getStyle('G'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('G'.$j)->getFill()->getStartColor()->setARGB('438eb9');
                    $objSheet->getStyle('H'.$j)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);                    
                    $objSheet->getStyle('H'.$j)->getFill()->getStartColor()->setARGB('438eb9');

                    $objSheet->setCellValue("B".$j,$val['agent']);
                    $j++;
                        foreach($val['room'] as $k=>$v){
                            $index=$k+1;
                            $room="";
                                    if($v["room_type"]==1){ 
                                         $room="Single";
                                    }elseif ($v["room_type"]==2) {
                                         $room="Double";
                                    }elseif ($v["room_type"]==3) {
                                         $room="Triple";
                                    }elseif ($v["room_type"]==4) {
                                         $room="Twin";
                                    }
                            $objSheet->setCellValue("B".$j,$index)->setCellValue("C".$j,$room)->setCellValue("E".$j,$v['guests']);
                            $j++;                
                        }
            }       
        $objWriter = IOFactory :: createWriter($objPHPExcel, 'Excel2007'); 
        // Sending headers to force the user to download the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//输出2007格式的excel        
        header('Content-Disposition: attachment;filename="Hotel_'.$query['tour_code'].'.xlsx"');//告诉浏览器将输出文件的名称
        header('Cache-Control: max-age=0');//禁止缓存
 
        $objWriter -> save('php://output');
    } 


        // 生成房间信息表
                        function room_list($t_id){                           
                        
                            //获取本团的信息
                            $res = $this->Group_model->get_group($t_id);                           

                            //获取订单信息
                            $tourCode = $res['t_tourCode'];

                            $Grouptotal = $this->Order_model->get_group_total($tourCode);
                         
                            //组装数据                          
                            $data = array();
                            $data['tour_code']      = $tourCode;
                            $data['adult_num']      = $Grouptotal[0]['adultNumber'];            
                            $data['infant_num']     = $Grouptotal[0]['infantNumber'];
                            $data['child_1_num']    = $Grouptotal[0]['childNumber1'];
                            $data['child_2_num']    = $Grouptotal[0]['childNumber2'];
                            $data['total_people'] = $Grouptotal[0]['totalNumber'];

                            $room_people="Total:".$data['total_people']."--";
                            if($data["adult_num"]>0){
                                $room_people=$room_people."Adult×".$data["adult_num"].",";
                                };
                            if($data["child_1_num"]>0){
                                 $room_people=$room_people."Child(no bed)×".$data["child_1_num"].",";
                                }; 
                            if($data["child_2_num"]>0){
                                $room_people=$room_people."Child(with bed)×".$data["child_2_num"].",";
                                };
                            if($data["infant_num"]>0){
                                $room_people=$room_people."Infant×".$data["infant_num"];
                                };
                            $data['room_people'] = $room_people;

                            $data['triple'] = $Grouptotal[0]['triple'];
                            $data['doubleroom']     = $Grouptotal[0]['doubleroom'];
                            $data['twin']   = $Grouptotal[0]['twin'];
                            $data['single']     = $Grouptotal[0]['single'];

                            $room_request="";
                            if($data["single"]>0){
                                 $room_request=$room_request."Single ×".$data["single"].",";
                                }; 
                            if($data["triple"]>0){
                                $room_request=$room_request."Triple ×".$data["triple"].",";
                                };
                            if($data["doubleroom"]>0){
                                $room_request=$room_request."Double ×".$data["doubleroom"].",";
                                };
                            if($data["twin"]>0){
                                $room_request=$room_request."Twin×".$data["twin"];
                                };
                            $data['room_request'] = $room_request;
                            $childn="";
                            $childy="";      

                            
                            //处理房间信息,获得本团的所有订单号
                            $res1 = $this->Order_model->get_all_order_id($tourCode);                            
                                                    
                            $data2=array();
                            $i=0;
                            $j=0;
                            foreach($res1 as $k => $v){
                                //遍历订单号
                                $o_id = $v['o_id'];
                                //找出本订单的agent所在的区域
                                $company_res =  $this->Order_model->get_company_id($o_id);
                                $company_id = $company_res[0]['a_id'];
                                $area_res =  $this->Order_model->get_company_area($company_id);
                                
                                $invoice_res=$this->Order_model->get_order_detail($o_id);
                                $area_id = $area_res[0]['a_area'];


                                $c_name = $area_res[0]['a_name'];
                                                        
                                if($area_id==2){
                                    $cc='NZAG:'.$c_name.'/ Invoice No:'.$invoice_res[0]['o_sn'] ;
                                    //本单是携程的单子
                                }elseif($area_id==3 && $company_id==37){
                                    $cc=$c_name.'/ Invoice No:'.$invoice_res[0]['o_sn']."/ CTRIP,VERY VERY IMPORTANT,PLEASE DOUBLE CHECK!!" ;
                                }else{
                                    $cc=$c_name.'/ Invoice No:'.$invoice_res[0]['o_sn'] ;
                                }

                                //找出本订单的所有小孩详情
                                $guest_res =  $this->Order_model->get_order_guest($o_id);                                
                                       foreach($guest_res as $k => $v){
                                        if($v['g_type']==3){
                                            $childn=$childn.++$i.'.'.$v['g_firstname']."/".$v['g_lastname']."(age)".";";
                                        }elseif($v['g_type']==4){
                                            $childy=$childy.++$j.'.'.$v['g_firstname']."/".$v['g_lastname']."(age)".";";
                                        }                                                                             
                                    }


                                //找出房间信息                                   
                                $room_people = $this->Order_model->get_room_people($o_id);
                                $dd = array();                              
                                    foreach($room_people as $k => $v){
                                        $d = array(                                         
                                            "room_type" => $v['r_type'],
                                            "guests"=>$v['r_guests']
                                        );
                                        $dd[] = $d;
                                    }
                                    $room_order=array();                                    
                                    $room_order['agent']=$cc;
                                    $room_order['room']=$dd;
                                    
                                    $data2[]=$room_order;
                                    
                            }
                            
                            $data['room_list'] = $data2;
                            $data['child_detail']="";
                            if($childn!=""){
                                $data['child_detail'].="Child(No Bed): ".$childn;
                            }
                            if($childy!=""){
                                $data['child_detail'].="Child(With Bed): ".$childy;
                            }
                            return $data;
                        }

        //生成给导游的信息表 
    function tour_guide(){
        //获取本团的tourCode                     
        $t_id = $this->input->get("id",true);

        //将旅游团id传给导游信息表函数   
        $query = $this -> tour_guide_list($t_id);


        if (!$query)
            return false; 
        // Starting the PHPExcel library
        $this -> load -> library('PHPExcel');
        $this -> load -> library('PHPExcel/IOFactory');
 
        $objPHPExcel = new PHPExcel();
        $objPHPExcel -> getProperties() -> setCreator("Pacific Delight Ltd")-> setTitle("Customers List for TourGuide");
 
        $objPHPExcel -> setActiveSheetIndex(0); 
        $objSheet=$objPHPExcel->getActiveSheet();//获取当前活动sheet
        // Field names in the first row

        //本旅游团的基础信息        
        $objSheet->setCellValue('B1', '线路名称')->setCellValue('C1', $query['cName']);
        $objSheet->setCellValue('B2', 'Tour Code')->setCellValue('C2', $query['tour_code']);        
        $objSheet->setCellValue('B3', 'Pax:')->setCellValue('C3', $query['room_people']);
        $objSheet->setCellValue('B4', 'Child:')->setCellValue('C4', $query['child_detail']);
        $objSheet->setCellValue('B5', 'Room:')->setCellValue('C5', $query['room_request']);

        //本旅游团各订单的信息
        $objSheet->setCellValue('B7', 'ID')->setCellValue('C7', 'Room Type')->setCellValue('E7', 'Customers');      
        $j=8;
            foreach($query['order'] as $v){
                   //联系人信息
                    $objSheet->setCellValue("B".$j,'Contacts:')->setCellValue("C".$j,$v['contact']['contacts'])->setCellValue("E".$j,$v['contact']['mobile']);                    
                    $j++;

                    //游客的姓名性别信息
                    $objSheet->setCellValue("B".$j,'ID')->setCellValue("C".$j,'Customer Name')->setCellValue("E".$j,'Gender')->setCellValue("F".$j,'Type');
                    $j++;
                        foreach($v["guest_list"] as $k=>$g){
                            $index=$k+1;
                            $gender="";
                            $type="";
                                    if($g["g_gender"]==1){ 
                                     $gender="Male";
                                    }elseif ($g["g_gender"]==2){
                                     $gender="Female";
                                    }

                                    if($g["g_guestType"]==1){ 
                                       $type= "Adult";
                                    }elseif ($g["g_guestType"]==2) {
                                       $type="Infant";
                                    }elseif ($g["g_guestType"]==3) {
                                       $type= "Child(No Bed)";                                       
                                    }elseif ($g["g_guestType"]==4) {
                                       $type= "Child(With Bed)";                                       
                                    }
                            $objSheet->setCellValue("B".$j,$index)->setCellValue("C".$j,$g["g_firstname"]."/".$g["g_lastname"])->setCellValue("E".$j,$gender)->setCellValue("F".$j,$type);
                            $j++;
                         }

                    //游客的房间信息
                        $objSheet->setCellValue("B".$j,'Room Info:')->setCellValue("C".$j,$v["roomInfo"]['room_order']);                    
                    $j++; 

                    // 航班信息 
                    $objSheet->setCellValue("B".$j,'ID')->setCellValue("C".$j,'Flight Date')->setCellValue("D".$j,'Flight No.')->setCellValue("E".$j,'Flight Route')
                    ->setCellValue("F".$j,'Flight Time')->setCellValue("G".$j,'Customers');
                    $j++;
                        foreach($v["flightInfo"] as $n => $f){
                            $index=$n+1;                            
                            $objSheet->setCellValue("B".$j,$index)->setCellValue("C".$j,$f["g_arriveDate"])->setCellValue("D".$j,$f["a_flightno"])->setCellValue("E".$j,$f["a_airport"])
                            ->setCellValue("F".$j,$f["a_time"])->setCellValue("G".$j,$f["arrivedName"]);
                            $j++;
                         }

                        // OP的留言
                        $objSheet->setCellValue("B".$j,'Remark:')->setCellValue("C".$j,$v['contact']['opnote']);                    
                        $j=$j+2; 
                    }

        $objWriter = IOFactory :: createWriter($objPHPExcel, 'Excel2007'); 
        // Sending headers to force the user to download the file
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');//输出2007格式的excel        
        header('Content-Disposition: attachment;filename="Tourguide_'.$query['tour_code'].'.xlsx"');//告诉浏览器将输出文件的名称
        header('Cache-Control: max-age=0');//禁止缓存
 
        $objWriter -> save('php://output');
    } 


        // 生成给导游的信息表
                        function tour_guide_list($t_id){ 

                            //获取本团的名称和日期
                            $res2 = $this->Group_model->get_tourgroup_name($t_id);
                            
                            $data = array();
                            $data['order']= array();
                            $data['date']       = $res2[0]['t_date'];           
                            $data['cName']      = $res2[0]['r_cName'];
                            $data['eName']      = $res2[0]['r_eName'];

                            //获取本团的总计游客和房间信息
                            $res = $this->Group_model->get_group($t_id);                            
                            $tourCode = $res['t_tourCode'];                         
                            $Grouptotal = $this->Order_model->get_group_total($tourCode);


                            //组装数据  
                            $data['tour_code']      = $tourCode;
                            $data['adult_num']      = $Grouptotal[0]['adultNumber'];            
                            $data['infant_num']     = $Grouptotal[0]['infantNumber'];
                            $data['child_1_num']    = $Grouptotal[0]['childNumber1'];
                            $data['child_2_num']    = $Grouptotal[0]['childNumber2'];
                            $data['total_people'] = $Grouptotal[0]['totalNumber'];

                            $room_people="Total:".$data['total_people']."/";
                            if($data["adult_num"]>0){
                                $room_people=$room_people."Adult×".$data["adult_num"].",";
                                };
                            if($data["child_1_num"]>0){
                                 $room_people=$room_people."Child(no bed)×".$data["child_1_num"].",";
                                }; 
                            if($data["child_2_num"]>0){
                                $room_people=$room_people."Child(with bed)×".$data["child_2_num"].",";
                                };
                            if($data["infant_num"]>0){
                                $room_people=$room_people."Infant×".$data["infant_num"];
                                };
                            $data['room_people'] = $room_people;

                            $data['triple'] = $Grouptotal[0]['triple'];
                            $data['doubleroom']  = $Grouptotal[0]['doubleroom'];
                            $data['twin']   = $Grouptotal[0]['twin'];
                            $data['single']     = $Grouptotal[0]['single']; 

                            $room_request="";
                            if($data["single"]>0){
                                 $room_request=$room_request."Single ×".$data["single"].",";
                                }; 
                            if($data["triple"]>0){
                                $room_request=$room_request."Triple ×".$data["triple"].",";
                                };
                            if($data["doubleroom"]>0){
                                $room_request=$room_request."Double ×".$data["doubleroom"].",";
                                };
                            if($data["twin"]>0){
                                $room_request=$room_request."Twin×".$data["twin"];
                                };
                            $data['room_request'] = $room_request;

                            //获取本团所有订单的id
                            $res1 = $this->Order_model->get_all_order_id($tourCode);

                            //遍历订单id，获取每一个订单的详情
                            $gf = array();
                             $childn="";
                             $b=0;                             
                             $childy=""; 
                             $y=0;
                            foreach($res1 as $k => $v){                             

                                $o_id = $v['o_id'];

                            //获取本订单的联系人信息和OP的审核信息
                                $data1= array();
                                $order_details = $this->Order_model->get_order_detail($o_id);
                                

                                $data1['contacts'] = $order_details[0]['o_contacts'];
                                $data1['mobile']   = $order_details[0]['o_mobile'];
                                $data1['opnote']   = $order_details[0]['o_opNote']; 

                                
                            //获取本订单的所有房间信息
                                $data2= array();                                
                                $data2['single']    = $order_details[0]['o_single'];
                                $data2['doubleroom']    = $order_details[0]['o_double'];
                                $data2['triple']    = $order_details[0]['o_triple'];
                                $data2['twin']      = $order_details[0]['o_twin'];

                                $room_order="";
                            if($data2["single"]>0){
                                 $room_order=$room_order."Single ×".$data2["single"].",";
                                }; 
                            if($data2["triple"]>0){
                                $room_order=$room_order."Triple ×".$data2["triple"].",";
                                };
                            if($data2["doubleroom"]>0){
                                $room_order=$room_order."Double ×".$data2["doubleroom"].",";
                                };
                            if($data2["twin"]>0){
                                $room_order=$room_order."Twin×".$data2["twin"];
                                };
                            $data2['room_order'] = $room_order; 


                            //获取本订单的所有游客信息
                                $data3 = array();           
                                $guest = $this->Order_model->get_order_guest($o_id);                                        
                                
                                $ga = array();                          
                                foreach($guest as $key => $v){
                                    if($v['g_type']==3){
                                        $childn=$childn.++$b.'.'.$v['g_firstname']."/".$v['g_lastname']."(age)".";";
                                    }elseif($v['g_type']==4){
                                        $childy=$childy.++$y.'.'.$v['g_firstname']."/".$v['g_lastname']."(age)".";";
                                    }
                                    $person = array(
                                        "g_firstname"=>$v['g_firstname'],
                                        "g_lastname"=>$v['g_lastname'],
                                        "g_gender"=>$v['g_gender'],
                                        "g_guestType"=>$v['g_type']
                                    );
                                    $ga[] = $person;
                                }
                                $data3 = $ga;

                                    //获取本订单的所有航班信息
                                    $data4 = array();   
                                    $flight = $this->Order_model->get_order_flight($o_id);
                                    $gd     = array();

                                    foreach($flight as $k => $v){
                                        $fl = array(
                                            "g_arriveDate"=> $v['f_date'],
                                            "a_flightno"=> $v['f_no'],
                                            "a_time"=>$v['f_time'],
                                            "a_airport"=>$v['f_route'],
                                            "arrivedName"=>$v['f_guest']
                                        );  
                                        $gd[]=$fl;
                                    }
                                    $data4 = $gd;
                                        $order2= array();
                                            
                                                    $order2["contact"]= $data1;
                                                    $order2["roomInfo"]= $data2;
                                                    $order2["guest_list"]=$data3;
                                                    $order2["flightInfo"]=$data4;                                           
                                        $gf[] = $order2;
                                    }
                                    $data['order'] = $gf;

                                $data['child_detail']="";
                            if($childn!=""){
                                $data['child_detail'].="Child(No Bed): ".$childn;
                              }
                            if($childy!=""){
                                $data['child_detail'].="Child(With Bed): ".$childy;
                              }

                                return $data;
                        }


}