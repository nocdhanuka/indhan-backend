package com.dhanuka.indhan.SelfParticipationActivity.Model;

import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.Date;

/**
 * Created by John on 9/27/2018.
 */
public class TicketList implements Serializable, Comparable<TicketList> {

    @SerializedName("name")
    @Expose
    private String name;
    @SerializedName("tID")
    @Expose
    private Integer tID;
    @SerializedName("ticket")
    @Expose
    private String ticket;
    @SerializedName("datetime_format")
    @Expose
    private String datetimeFormat;
    @SerializedName("data_source")
    @Expose
    private String dataSource;
    @SerializedName("state")
    @Expose
    private String state;
    @SerializedName("district")
    @Expose
    private String district;
    @SerializedName("tehsil")
    @Expose
    private String tehsil;
    @SerializedName("product_1")
    @Expose
    private String product1;
    @SerializedName("product_2")
    @Expose
    private String product2;
    @SerializedName("product_3")
    @Expose
    private String product3;
    @SerializedName("crop_1")
    @Expose
    private String crop1;
    @SerializedName("crop_2")
    @Expose
    private String crop2;
    @SerializedName("crop_3")
    @Expose
    private String crop3;
    @SerializedName("base_url")
    @Expose
    private String baseUrl;
    @SerializedName("uploaded_file")
    @Expose
    private String uploadedFile;
    @SerializedName("description")
    @Expose
    private String description;
    @SerializedName("village")
    @Expose
    private String village;

    private Date date;

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public Integer gettID() {
        return tID;
    }

    public void settID(Integer tID) {
        this.tID = tID;
    }

    public String getVillage() {
        return village;
    }

    public void setVillage(String village) {
        this.village = village;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Integer getTID() {
        return tID;
    }

    public void setTID(Integer tID) {
        this.tID = tID;
    }

    public String getTicket() {
        return ticket;
    }

    public void setTicket(String ticket) {
        this.ticket = ticket;
    }

    public String getDatetimeFormat() {
        return datetimeFormat;
    }

    public void setDatetimeFormat(String datetimeFormat) {
        this.datetimeFormat = datetimeFormat;
    }

    public String getDataSource() {
        return dataSource;
    }

    public void setDataSource(String dataSource) {
        this.dataSource = dataSource;
    }

    public String getState() {
        return state;
    }

    public void setState(String state) {
        this.state = state;
    }

    public String getDistrict() {
        return district;
    }

    public void setDistrict(String district) {
        this.district = district;
    }

    public String getTehsil() {
        return tehsil;
    }

    public void setTehsil(String tehsil) {
        this.tehsil = tehsil;
    }

    public String getProduct1() {
        return product1;
    }

    public void setProduct1(String product1) {
        this.product1 = product1;
    }

    public String getProduct2() {
        return product2;
    }

    public void setProduct2(String product2) {
        this.product2 = product2;
    }

    public String getProduct3() {
        return product3;
    }

    public void setProduct3(String product3) {
        this.product3 = product3;
    }

    public String getCrop1() {
        return crop1;
    }

    public void setCrop1(String crop1) {
        this.crop1 = crop1;
    }

    public String getCrop2() {
        return crop2;
    }

    public void setCrop2(String crop2) {
        this.crop2 = crop2;
    }

    public String getCrop3() {
        return crop3;
    }

    public void setCrop3(String crop3) {
        this.crop3 = crop3;
    }

    public String getBaseUrl() {
        return baseUrl;
    }

    public void setBaseUrl(String baseUrl) {
        this.baseUrl = baseUrl;
    }

    public String getUploadedFile() {
        return uploadedFile;
    }

    public void setUploadedFile(String uploadedFile) {
        this.uploadedFile = uploadedFile;
    }

    public String getDescription() {
        return description;
    }

    public void setDescription(String description) {
        this.description = description;
    }

    @Override
    public int compareTo(@NonNull TicketList ticketList) {
        try {
            return getDate().compareTo(ticketList.getDate());
        } catch (Exception e) {
            return 1;
        }
    }
}
