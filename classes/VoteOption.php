<?php
/**
 * Created by PhpStorm.
 * User: carlosherrera
 * Date: 30/8/18
 * Time: 4:31 PM
 */

/**
 * Class VoteOption
 * @property string id
 * @property string label
 * @property string name
 * @property string custom_img
 */
class VoteOption {
	private $totalVotes;
	private $row = [];
	private $_type;
	private $_result;
	private $_vote_link;
	private $_votes;
	private $_allow_link;

	/**
	 * VoteOption constructor.
	 *
	 * @param array $row
	 */
	function __construct( Array $row = [] ) {
		$this->row = $row;
	}

	/**
	 * @param $name
	 *
	 * @return mixed|null
	 */
	function __get( $name ) {
		return array_key_exists( $name, $this->row ) ? $this->row[ $name ] : null;
	}

	/**
	 * @param $name
	 * @param $value
	 *
	 * @return $this
	 */
	function __set( $name, $value ) {
		$this->row[ $name ] = $value;

		return $this;
	}

	function __sleep() {
		// TODO: Implement __sleep() method.
		return $this->row;
	}

	/**
	 * @return mixed
	 */
	public function getVoteLink( $ID, $user_ID ) {
		$callback         = $this->getCallbackAddVote();
		$this->_vote_link = sprintf( "<a onclick=\"%s('%s','%s','%s',this);\">%s</a>", $callback, $ID, $this->id,
			$user_ID,
			$this->getImage() );

		return $this->_vote_link;
	}

	public function getCallbackAddVote() {
		return $this->getType() == 'post' ? 'simplevotemeaddvote' : 'simplevotemeaddvotecompliment';
	}

	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->_type;
	}

	/**
	 * @param mixed $type
	 */
	public function setType( $type ): void {
		$this->_type = $type;
	}

	public function getImage() {
		return gt_simplevoteme_getimgvote( $this );
	}

	/**
	 * @return mixed
	 */
	public function getAllowLink() {
		return $this->_allow_link;
	}

	/**
	 * @param mixed $allow_link
	 */
	public function setAllowLink( $allow_link ): void {
		$this->_allow_link = $allow_link;
	}

	public function toArray() {
		// TODO: Implement __toString() method.
		return ( array_merge( $this->row, [ 'result' => $this->getResult(), 'votes' => $this->getVotes() ] ) );
	}


	public function getResult() {
		if ( ! $this->_result ) {
			$this->setResult();
		}

		return $this->_result;
	}

	public function setResult( $result = null ) {
		if ( $result ) {
			$this->_result = $result;
		} else {
			$votemeResultsType = get_option( 'gt_simplevoteme_results_type' );
			if ( $votemeResultsType == 2 )//just total votes
			{
				$this->_result = $this->countVotes();
			} else if ( $votemeResultsType == 1 )//only percentages
			{
				$this->_result = $this->percentVotes();
			} else //all
			{
				$this->_result = sprintf( "%s<small> (%s) </small>", $this->countVotes(), $this->percentVotes() );
			}
		}
	}

	/**
	 * @return mixed
	 */
	public function getVotes() {
		return $this->_votes;
	}

	public function setVotes( $votes ) {
		if ( array_key_exists( $this->id, $votes ) ) {
			$this->_votes     = $votes[ $this->id ];
			$this->totalVotes = sizeof( $votes, 1 ) - sizeof( $votes, 0 );
		} else {
			$this->_votes = [];
		}

		return $this;


	}

	public function countVotes() {
		return count( $this->getVotes() );
	}

	public function percentVotes() {
		return ( round( $this->countVotes() / $this->totalVotes, 0 ) * 100 ) . '%';
	}

}