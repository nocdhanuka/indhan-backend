package com.dhanuka.indhan.DataBase.TableDataModel;

/**
 * Created by John on 5/5/2018.
 */
public class ImageOfflineModel {

    int id;
    int isUploaded;
    String image_url_local;
    String lattitude;
    String longitude;
    String ticket_id;
    String type;
    String description;
    String date;
    int isTicketOffline;

    public int getIsTicketOffline() {
        return isTicketOffline;
    }

    public void setIsTicketOffline(int isTicketOffline) {
        this.isTicketOffline = isTicketOffline;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    public String getType() {
        return type;
    }

    public void setType(String type) {
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

    public String getLattitude() {
        return lattitude;
    }

    public void setLattitude(String lattitude) {
        this.lattitude = lattitude;
    }

    public String getLongitude() {
        return longitude;
    }

    public void setLongitude(String longitude) {
        this.longitude = longitude;
    }

    public String getTicket_id() {
        return ticket_id;
    }

    public void setTicket_id(String ticket_id) {
        this.ticket_id = ticket_id;
    }

}
