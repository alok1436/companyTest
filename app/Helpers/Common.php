<?php 

function totaldownlinemembers($user_id)// Recursive function to get all of the children...unlimited depth
{

	global $exclude, $depth;			// Refer to the global array defined at the top of this script
    $tempTree = '';
    
    $childs = App\User::where('reseller_id',$user_id)->get()->toArray();
    
	if(!empty($childs)){
	    
        foreach($childs as $child)
    	{
    		if ( $child['id'] != $child['reseller_id'] )
    
    		{
    
    			for ( $c=0;$c<$depth;$c++ )			// Indent over so that there is distinction between levels
    
    			{ $tempTree .= ""; }
    
    			$tempTree .= $child['id'].'~';
    
    			$depth++;		// Incriment depth b/c we're building this child's child tree  (complicated yet???)
    
    			$tempTree .= totaldownlinemembers($child['id']);	 
    
    		} 
    	}
	} 
	return $tempTree;
}
/*Downline Team*/
function myTeamMembers($user_id)// Recursive function to get all of the children...unlimited depth
{ 
	global $exclude, $depth; // Refer to the global array defined at the top of this script
    $tempTree2 = '';
	
    $childs2 = App\User::where('reseller_id',$user_id)->get()->toArray();
    
	if(!empty($childs2)){
	    
        foreach($childs2 as $child2)
    	{

    		if ( $child2['id'] != $child2['reseller_id'] )
    		{
    
    			for ( $c=0;$c<$depth;$c++ )// Indent over so that there is distinction between levels
    			{ $tempTree2 .= ""; }
    
    			$tempTree2 .= $child2['id'].'~';
    
    			$depth++;		// Incriment depth b/c we're building this child's child tree  (complicated yet???)
    
    			$tempTree2 .= totaldownlinemembers($child2['id']);	 
    		} 
	    } 	
	} 
	return $tempTree2; 
} 

function totaldownlinemembersBetweenDates($user_id,$from_date,$to_date)// Recursive function to get all of the children...unlimited depth
{
	global $exclude, $depth;// Refer to the global array defined at the top of this script
    $tempTree = '';
    
    $childs = App\User::where('reseller_id',$user_id)->get()->toArray();
            
	if(!empty($childs)){
	    
        foreach($childs as $child)
    	{
    		if ( $child['id'] != $child['reseller_id'] )
    		{
    
    			for ( $c=0;$c<$depth;$c++ )			// Indent over so that there is distinction between levels
    
    			{ $tempTree .= ""; }
    
    		    if($child['created_at'] >= $from_date && $child['created_at'] <= $to_date){
    			    $tempTree .= $child['id'].'~';
                }
    
    			$depth++;		// Incriment depth b/c we're building this child's child tree  (complicated yet???)
    
    			$tempTree .= totaldownlinemembersBetweenDates($child['id'],$from_date,$to_date);	 
    
    		} 
    	}
	} 
	return $tempTree;
}

/*Team of between dates*/
function myTeamMembersBetweenDates($user_id,$from_date,$to_date)// Recursive function to get all of the children...unlimited depth
{ 
	global $exclude, $depth; // Refer to the global array defined at the top of this script
    $tempTree2 = '';
	
    $childs2 = App\User::where('reseller_id',$user_id)->get()->toArray();
    
	if(!empty($childs2)){
	    
        foreach($childs2 as $child2)
    	{

    		if ( $child2['id'] != $child2['reseller_id'] )
    		{
    
    			for ( $c=0;$c<$depth;$c++ )// Indent over so that there is distinction between levels
    			{ $tempTree2 .= ""; }
                
                if($child2['created_at'] >= $from_date && $child2['created_at'] <= $to_date){
    			    $tempTree2 .= $child2['id'].'~';
                }
    
    			$depth++;		// Incriment depth b/c we're building this child's child tree  (complicated yet???)
                
    			$tempTree2 .= totaldownlinemembersBetweenDates($child2['id'],$from_date,$to_date);	 
    		} 
	    } 	
	} 
	return $tempTree2; 
} 
function myDirectMembers($user_id){
    
    $childs = App\User::where('reseller_id',$user_id)->get()->toArray();
    return $childs;
}

function myDirectMembersBetweenDates($user_id,$from_date,$to_date){
    
    $childs = App\User::where('reseller_id',$user_id)
            ->where('id','!=',$user_id)
            ->whereBetween('created_at', [$from_date, $to_date])
            ->get()
            ->toArray();
    return $childs;
}

function totaldownlineCount($user_id)
{
	$tempTree = totalteammembers($user_id);
	return substr_count($tempTree,'~');
}

function getDirectPercentage($no_of_joining){
    
    $sql = "SELECT dm_percentage FROM membership_levels WHERE direct_member>=$no_of_joining ORDER BY direct_member ASC LIMIT 1";
    $row = DB::select($sql);
    if(!empty($row)){
        return $row[0]->dm_percentage;
    }else{
        return 0;
    }
}
function getTeamPercentage($no_of_joining){
    
    $sql = "SELECT tm_percentage FROM membership_levels WHERE team_member>=$no_of_joining ORDER BY team_member ASC LIMIT 1";
    $row = DB::select($sql);
    if(!empty($row)){
        return $row[0]->tm_percentage;
    }else{
        return 0;
    }
}
function convert_number($number) {
    
	if (($number < 0) || ($number > 999999999)) {
		throw new Exception("Number is out of range");
	}
	$Gn = floor($number / 1000000);
	/* Millions (giga) */
	$number -= $Gn * 1000000;
	$kn = floor($number / 1000);
	/* Thousands (kilo) */
	$number -= $kn * 1000;
	$Hn = floor($number / 100);
	/* Hundreds (hecto) */
	$number -= $Hn * 100;
	$Dn = floor($number / 10);
	/* Tens (deca) */
	$n = $number % 10;
	/* Ones */
	$res = "";
	if ($Gn) {
		$res .= $this->convert_number($Gn) .  "Million";
	}
	if ($kn) {
		$res .= (empty($res) ? "" : " ") .$this->convert_number($kn) . " Thousand";
	}
	if ($Hn) {
		$res .= (empty($res) ? "" : " ") .$this->convert_number($Hn) . " Hundred";
	}
	$ones = array("", "One", "Two", "Three", "Four", "Five", "Six", "Seven", "Eight", "Nine", "Ten", "Eleven", "Twelve", "Thirteen", "Fourteen", "Fifteen", "Sixteen", "Seventeen", "Eightteen", "Nineteen");
	$tens = array("", "", "Twenty", "Thirty", "Fourty", "Fifty", "Sixty", "Seventy", "Eigthy", "Ninety");
	if ($Dn || $n) {
		if (!empty($res)) {
			$res .= " and ";
		}
		if ($Dn < 2) {
			$res .= $ones[$Dn * 10 + $n];
		} else {
			$res .= $tens[$Dn];
			if ($n) {
				$res .= "-" . $ones[$n];
			}
		}
	}
	if (empty($res)) {
		$res = "zero";
	}
	return $res;
}