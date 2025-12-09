package com.dhanuka.indhan.RegisterFarmerScreen.ifcFarmerRegistration.model;

import androidx.room.Entity;
import androidx.room.Index;
import androidx.room.PrimaryKey;

import java.io.Serializable;

@Entity(indices = {@Index(value = {"serverId"}, unique = true)})
public class AddIfcFarmer implements Serializable {

    public int id;
    @PrimaryKey()
    public int serverId;
    public int status = 0;
    public boolean uploaded = false;
    public String farmer_name = "";
    public String mobile;
    public String image_date_time;
    public String acrers;
    public String image;
    public String imageName;
    public String ticket_no = "";
    public double latitude = 0.0;
    public double longitude = 0.0;
    public String serverTime;
    public double activityDistance = 0.0;

    public int getId() {
        return id;
    }

    public double getActivityDistance() {
        return activityDistance;
    }

    public void setActivityDistance(double activityDistance) {
        this.activityDistance = activityDistance;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getStatus() {
        return status;
    }

    public void setStatus(int status) {
        this.status = status;
    }

    public String getFarmer_name() {
        return farmer_name;
    }

    public void setFarmer_name(String farmer_name) {
        this.farmer_name = farmer_name;
    }

    public String getMobile() {
        return mobile;
    }

    public void setMobile(String mobile) {
        this.mobile = mobile;
    }

    public String getImage_date_time() {
        return image_date_time;
    }

    public void setImage_date_time(String image_date_time) {
        this.image_date_time = image_date_time;
    }

    public String getAcrers() {
        if (acrers == null) {
            acrers = "0";
        }
        return acrers;
    }

    public void setAcrers(String acrers) {
        this.acrers = acrers;
    }

    public String getImage() {
        return image;
    }

    public void setImage(String image) {
        this.image = image;
    }

    public String getImageName() {
        return imageName;
    }

    public void setImageName(String imageName) {
        this.imageName = imageName;
    }

    public String getTicket_no() {
        return ticket_no;
    }

    public void setTicket_no(String ticket_no) {
        this.ticket_no = ticket_no;
    }

    public double getLatitude() {
        return latitude;
    }

    public void setLatitude(double latitude) {
        this.latitude = latitude;
    }

    public double getLongitude() {
        return longitude;
    }

    public void setLongitude(double longitude) {
        this.longitude = longitude;
    }

    public String getServerTime() {
        return serverTime;
    }

    public void setServerTime(String serverTime) {
        this.serverTime = serverTime;
    }
}
