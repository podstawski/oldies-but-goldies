<?php

class Room extends AclModel
{
    static $has_many = array(
        array('lessons')
    );

    static $belongs_to = array(
        array('training_center')
    );

    static $validates_presence_of = array(
        array('training_center_id'),
        array('name'),
        array('symbol'),
        array('available_space')
    );

    static $after_save = array('update_training_center_rooms_and_seats');

    public function update_training_center_rooms_and_seats()
    {
        $rooms = Room::all(array(
            'conditions' => array('training_center_id = ?', $this->training_center_id)
        )) ?: array();

        $seats = 0;
        foreach ($rooms as $room) {
            $seats += intval($room->available_space);
        }
        $this->training_center->update_attributes(array(
            'room_amount' => count($rooms),
            'seats_amount' => $seats
        ));
    }
}
