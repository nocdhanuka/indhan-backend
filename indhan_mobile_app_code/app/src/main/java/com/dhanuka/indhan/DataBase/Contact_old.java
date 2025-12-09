package com.dhanuka.indhan.DataBase;

/**
 * Created by Shivam on 3/21/2018.
 */

public class Contact_old {

    int _id;
    String _time;
    String _phone_number;
    int status;

    // Empty constructor
    public Contact_old() {

    }

    // constructor
    public Contact_old(int id, String time, String _phone_number, int status) {
        this._id = id;
        this.status = status;
        this._time = time;
        this._phone_number = _phone_number;
    }

    // constructor
    public Contact_old(String time, String _phone_number, int status) {
        this.status = status;
        this._time = time;
        this._phone_number = _phone_number;
    }

    public int getID() {
        return this._id;
    }

    // setting id
    public void setID(int id) {
        this._id = id;
    }

    // getting time
    public String getName() {
        return this._time;
    }

    // setting time
    public void setName(String time) {
        this._time = time;
    }

    // getting phone number
    public String getPhoneNumber() {
        return this._phone_number;
    }

    // setting phone number
    public void setPhoneNumber(String phone_number) {
        this._phone_number = phone_number;
    }

    public int getStatus() {
        return this.status;
    }

    // setting id
    public void setStatus(int status) {
        this.status = status;
    }
}
