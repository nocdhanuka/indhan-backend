package com.dhanuka.indhan.Activity.offlineClaimTicket;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

//type = from ticket = 1, from self participation = 2

@Entity
public class ManualTicketClaimImage {
    @PrimaryKey(autoGenerate = true)
    int id;
    int isUploaded;
    String image_url_local;
    String latitude;
    String longitude;
    String ticket_number;
    String description;
    String date;
    Long lastHitTime;
    int type;
    String departure_time;
    String departure_comment;
    String departure_imageURL;
    String duration;
    String departure_latitude;
    String departure_longitude;

    public String getDeparture_time() {
        return departure_time;
    }

    public void setDeparture_time(String departure_time) {
        this.departure_time = departure_time;
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

    public String getDuration() {
        return duration;
    }

    public void setDuration(String duration) {
        this.duration = duration;
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

    public Long getLastHitTime() {
        return lastHitTime;
    }

    public void setLastHitTime(Long lastHitTime) {
        this.lastHitTime = lastHitTime;
    }

    public String getTicket_number() {
        return ticket_number;
    }

    public void setTicket_number(String ticket_number) {
        this.ticket_number = ticket_number;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public int getType() {
        return type;
    }

    public void setType(int type) {
        this.type = type;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    public int getIsUploaded() {
        return isUploaded;
    }

    public void setIsUploaded(int isUploaded) {
        this.isUploaded = isUploaded;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public String getImage_url_local() {
        return image_url_local;
    }

    public void setImage_url_local(String image_url_local) {
        this.image_url_local = image_url_local;
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
