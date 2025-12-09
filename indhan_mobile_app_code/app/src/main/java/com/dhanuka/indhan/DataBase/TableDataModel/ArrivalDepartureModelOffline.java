package com.dhanuka.indhan.DataBase.TableDataModel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

@Entity
public class ArrivalDepartureModelOffline {

    @PrimaryKey(autoGenerate = true)
    int id;

    private String ticket_id;

    private String ticket_no;

    //value = 0 for offline , 1 for online
    private String isOnline;

    private String comment;

    private String imageURL;

    private String token;

    private String latitude;

    private String longitude;

    private String arrival_time;

    private String departure_time;

    private String departure_comment = "";

    private String departure_imageURL;

    private String duration;

    private String departure_latitude;

    private String departure_longitude;

    int type = 0;

    int isUploaded = 0;

    Long lastHitTime = 0L;

    public String getTicket_no() {
        return ticket_no;
    }

    public void setTicket_no(String ticket_no) {
        this.ticket_no = ticket_no;
    }

    public String getIsOnline() {
        return isOnline;
    }

    public void setIsOnline(String isOnline) {
        this.isOnline = isOnline;
    }

    public String getArrival_time() {
        return arrival_time;
    }

    public void setArrival_time(String arrival_time) {
        this.arrival_time = arrival_time;
    }

    public String getDeparture_time() {
        return departure_time;
    }

    public void setDeparture_time(String departure_time) {
        this.departure_time = departure_time;
    }

    public String getDuration() {
        return duration;
    }

    public void setDuration(String duration) {
        this.duration = duration;
    }

    public String getDeparture_comment() {
        return departure_comment;
    }

    public void setDeparture_comment(String departure_comment) {
        this.departure_comment = departure_comment;
    }

    public String getDeparture_imageURL() {
        return departure_imageURL;
    }

    public void setDeparture_imageURL(String departure_imageURL) {
        this.departure_imageURL = departure_imageURL;
    }

    public String getDeparture_latitude() {
        return departure_latitude;
    }

    public void setDeparture_latitude(String departure_latitude) {
        this.departure_latitude = departure_latitude;
    }

    public String getDeparture_longitude() {
        return departure_longitude;
    }

    public void setDeparture_longitude(String departure_longitude) {
        this.departure_longitude = departure_longitude;
    }

    public int getIsUploaded() {
        return isUploaded;
    }

    public void setIsUploaded(int isUploaded) {
        this.isUploaded = isUploaded;
    }

    public Long getLastHitTime() {
        return lastHitTime;
    }

    public void setLastHitTime(Long lastHitTime) {
        this.lastHitTime = lastHitTime;
    }

    public int getType() {
        return type;
    }

    public void setType(int type) {
        this.type = type;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getTicket_id() {
        return ticket_id;
    }

    public void setTicket_id(String ticket_id) {
        this.ticket_id = ticket_id;
    }

    public String getComment() {
        return comment;
    }

    public void setComment(String comment) {
        this.comment = comment;
    }

    public String getImageURL() {
        return imageURL;
    }

    public void setImageURL(String imageURL) {
        this.imageURL = imageURL;
    }

    public String getToken() {
        return token;
    }

    public void setToken(String token) {
        this.token = token;
    }

    public String getLatitude() {
        return latitude;
    }

    public void setLatitude(String latitude) {
        this.latitude = latitude;
    }

    public String getLongitude() {
        return longitude;
    }

    public void setLongitude(String longitude) {
        this.longitude = longitude;
    }
}
