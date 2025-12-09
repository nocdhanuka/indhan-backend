package com.dhanuka.indhan.DataBase;

/**
 * Created by Shivam on 3/21/2018.
 */

public class Contact {

    int _id;
    int autoid;
    String _time;
    String _phone_number;
    int status;
    String timestamp;
    String isSync;
    String isDeleted;
    String type;
    String last_modified;

    // Empty constructor
    public Contact() {

    }

    // constructor
    public Contact(int id, String time, String _phone_number, int status) {
        this._id = id;
        this.status = status;
        this._time = time;
        this._phone_number = _phone_number;
    }

    // constructor
    public Contact(String time, String _phone_number, int status) {
        this.status = status;
        this._time = time;
        this._phone_number = _phone_number;
    }

    public int getAutoid() {
        return autoid;
    }

    public void setAutoid(int autoId) {
        this.autoid = autoId;
    }

    public int get_id() {
        return _id;
    }

    public void set_id(int _id) {
        this._id = _id;
    }

    public String get_time() {
        return _time;
    }

    public void set_time(String _time) {
        this._time = _time;
    }

    public String get_phone_number() {
        return _phone_number;
    }

    public void set_phone_number(String _phone_number) {
        this._phone_number = _phone_number;
    }

    public String getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(String timestamp) {
        this.timestamp = timestamp;
    }

    public String getIsSync() {
        return isSync;
    }

    public void setIsSync(String isSync) {
        this.isSync = isSync;
    }

    public String getIsDeleted() {
        return isDeleted;
    }

    public void setIsDeleted(String isDeleted) {
        this.isDeleted = isDeleted;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public String getLast_modified() {
        return last_modified;
    }

    public void setLast_modified(String last_modified) {
        this.last_modified = last_modified;
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
