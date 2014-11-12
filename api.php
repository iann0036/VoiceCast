<?php
	$APP_ID = '8X2K84-PE6PHRAPVR';
    $response = array();
	
	if (isset($_REQUEST['q']) && $_REQUEST['q']!==null) {
        if ($_REQUEST['q']=="who is your creator" || $_REQUEST['q']=="who is your owner" || $_REQUEST['q']=="who created you" || $_REQUEST['q']=="who made you") {
            echo json_encode(
                array(
                    'original_input' => $_REQUEST['q'],
                    'speech_url' => 'http://media.tts-api.com/13aa83346d03abfcac4786087c7d7964c8f16276.mp3',
                    'pods' => array(
                        array(
                            'title' => 'My creator',
                            'text' => 'Ian Mckay'
                        ),
                        array(
                            'title' => 'Picture of Ian',
                            'image' => 'https://cast.ian.mn/ian.jpg'
                        ),
                        array(
                            'title' => 'Facts about Ian',
                            'text' => '<p>He\'s 22 years old</p><p>He likes waffles</p>'
                        )
                    )
                )
            );
            die();
        }

        /* Setup */
		$input = $_REQUEST['q'];
        $response['original_input'] = $input;
		$query = urlencode($input);
		$raw = file_get_contents('http://api.wolframalpha.com/v2/query?input='.$query.'&appid='.$APP_ID);
		$xml = simplexml_load_string($raw);

        /* Text-to-speech */
		foreach ($xml->pod as $pod) {
			if ($pod['title']=="Result") {
				$answer = $pod->subpod->plaintext;
                $response['speech_url'] = file_get_contents('http://tts-api.com/tts.mp3?q='.urlencode($answer)."&return_url=1");
			}
		}
		if (!isset($response['speech_url']) && isset($xml->pod[1]->subpod->plaintext)) {
			$answer = $xml->pod[1]->subpod->plaintext;
            $response['speech_url'] = file_get_contents('http://tts-api.com/tts.mp3?q='.urlencode($answer)."&return_url=1");
		}

        /* Pod outputs */
        $response['pods'] = array();
        foreach ($xml->pod as $pod_i) {
            $pod = array();
            $pod['title'] = (string)$pod_i['title'];
            if (isset($pod_i->subpod->plaintext) && $pod_i->subpod->plaintext!="") {
                $pod['text'] = (string)$pod_i->subpod->plaintext;
            }
            if (isset($pod_i->subpod->img)) {
                $pod['image'] = (string)$pod_i->subpod->img['src'];
            }
            $response['pods'][] = $pod;
        }

        /* Output */
        echo json_encode($response);
	}
