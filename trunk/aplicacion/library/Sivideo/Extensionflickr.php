<?php
class Sivideo_Extencionflickr extends Zend_Service_Flickr
{
	 /**
	 * Recupera una lista de las fotos públicas
	 * Methods soporte:
	 *  flickr.contacts.getPublicList:			Get the contact list for a user.
	 *  flickr.favorites.getPublicList:			Returns a list of favorite public photos for the given user.
	 *  flickr.people.getPublicGroups:			Returns the list of public groups a user is a member of.
	 *  flickr.people.getPublicPhotos:			Get a list of public photos for the given user.
	 *  flickr.photos.getContactsPublicPhotos:	Fetch a list of recent public photos from a users' contacts.
	 * 
     *
     * Additional query options include:
     *
     *  # per_page:        how many results to return per query
     *  # page:            the starting page offset.  first result will be (page - 1) * per_page + 1
     *  # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *  # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *  # min_taken_date:  Minimum upload date to search on.  Date should be a MySQL datetime.
     *  # max_taken_date:  Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param  string $method  method of API
     * @param  string $query   username or email
     * @param  array  $options Additional parameters to refine your query.
     * @return Zend_Service_Flickr_ResultSet
     * @throws Zend_Service_Exception
     */
	
	public function superUserSearch($method, $query, array $options = null)
    {
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');


        // can't access by username, must get ID first
        if (strchr($query, '@')) {
            // optimistically hope this is an email
            $options['user_id'] = $this->getIdByEmail($query);
        } else {
            // we can safely ignore this exception here
            $options['user_id'] = $this->getIdByUsername($query);
        }

        $options = $this->_prepareOptions($method, $options, $defaultOptions);
        $this->_validateUserSearch($options);

        // now search for photos
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet('/services/rest/', $options);

        if ($response->isError()) {
            /**
             * @see Zend_Service_Exception
             */
            require_once 'Zend/Service/Exception.php';
            throw new Zend_Service_Exception('An error occurred sending request. Status code: '
                                           . $response->getStatus());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

		self::_checkErrors($dom);
		
		
        /**
         * @see Zend_Service_Flickr_ResultSet
         */
        require_once 'Zend/Service/Flickr/ResultSet.php';
        return new Zend_Service_Flickr_ResultSet($dom, $this);
    }
}