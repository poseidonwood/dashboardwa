<?php
require_once('../includes/vCard.php');

function vcf_to_string(vCard $vCard)
{
   $vcard_data='<h2>'.$vCard -> FN[0].'</h2>';

		if ($vCard -> PHOTO)
		{
			foreach ($vCard -> PHOTO as $Photo)
			{
				if ($Photo['Encoding'] == 'b')
				{
					$vcard_data.= '<img src="data:image/'.$Photo['Type'][0].';base64,'.$Photo['Value'].'" /><br />';
				}
				else
				{
					$vcard_data.= '<img src="'.$Photo['Value'].'" /><br />';
				}

				/*
				// It can also be saved to a file
				try
				{
					$vCard -> SaveFile('photo', 0, 'test_image.jpg');
					// The parameters are:
					//	- name of the file we want to save (photo, logo or sound)
					//	- index of the file in case of multiple files (defaults to 0)
					//	- target path to save to, including the filenam
				}
				catch (Exception $E)
				{
					// Target path not writable
				}
				*/
			}
		}

		foreach ($vCard -> N as $Name)
		{
			$vcard_data.= '<h3>Name: '.$Name['FirstName'].' '.$Name['LastName'].'</h3>';
		}

		foreach ($vCard -> ORG as $Organization)
		{
			$vcard_data.= '<h3>Organization: '.$Organization['Name'].
				($Organization['Unit1'] || $Organization['Unit2'] ?
					' ('.implode(', ', array($Organization['Unit1'], $Organization['Unit2'])).')' :
					''
				).'</h3>';
		}

		if ($vCard -> TEL)
		{
			$vcard_data.= '<p><h4>Phone</h4>';
			foreach ($vCard -> TEL as $Tel)
			{
				if (is_scalar($Tel))
				{
					$vcard_data.= $Tel.'<br />';
				}
				else
				{
					$vcard_data.= $Tel['Value'].' ('.implode(', ', $Tel['Type']).')<br />';
				}
			}
			$vcard_data.= '</p>';
		}

		if ($vCard -> EMAIL)
		{
			$vcard_data.= '<p><h4>Email</h4>';
			foreach ($vCard -> EMAIL as $Email)
			{
				if (is_scalar($Email))
				{
					$vcard_data.= $Email;
				}
				else
				{
					$vcard_data.= $Email['Value'].' ('.implode(', ', $Email['Type']).')<br />';
				}
			}
			$vcard_data.= '</p>';
		}

		if ($vCard -> URL)
		{
			$vcard_data.= '<p><h4>URL</h4>';
			foreach ($vCard -> URL as $URL)
			{
				if (is_scalar($URL))
				{
					$vcard_data.= $URL.'<br />';
				}
				else
				{
					$vcard_data.= $URL['Value'].'<br />';
				}
			}
			$vcard_data.= '</p>';
		}

		if ($vCard -> IMPP)
		{
			$vcard_data.= '<p><h4>Instant messaging</h4>';
			foreach ($vCard -> IMPP as $IMPP)
			{
				if (is_scalar($IMPP))
				{
					$vcard_data.= $IMPP.'<br />';
				}
				else
				{
					$vcard_data.= $IMPP['Value'].'<br/ >';
				}
			}
			$vcard_data.= '</p>';
		}

		if ($vCard -> ADR)
		{
			foreach ($vCard -> ADR as $Address)
			{
				$vcard_data.= '<p><h4>Address ('.implode(', ', $Address['Type']).')</h4>';
				$vcard_data.= 'Street address: <strong>'.($Address['StreetAddress'] ? $Address['StreetAddress'] : '-').'</strong><br />'.
					'PO Box: <strong>'.($Address['POBox'] ? $Address['POBox'] : '-').'</strong><br />'.
					'Extended address: <strong>'.($Address['ExtendedAddress'] ? $Address['ExtendedAddress'] : '-').'</strong><br />'.
					'Locality: <strong>'.($Address['Locality'] ? $Address['Locality'] : '-').'</strong><br />'.
					'Region: <strong>'.($Address['Region'] ? $Address['Region'] : '-').'</strong><br />'.
					'ZIP/Post code: <strong>'.($Address['PostalCode'] ? $Address['PostalCode'] : '-').'</strong><br />'.
					'Country: <strong>'.($Address['Country'] ? $Address['Country'] : '-').'</strong>';
			}
			$vcard_data.= '</p>';
		}

		if ($vCard -> AGENT)
		{
			$vcard_data.= '<h4>Agents</h4>';
			foreach ($vCard -> AGENT as $Agent)
			{
				if (is_scalar($Agent))
				{
					$vcard_data.= '<div class="Agent">'.$Agent.'</div>';
				}
				elseif (is_a($Agent, 'vCard'))
				{
					$vcard_data.= '<div class="Agent">';
					vcf_to_string($Agent);
					$vcard_data.= '</div>';
				}
			}
		}
   return $vcard_data;
}
?>
