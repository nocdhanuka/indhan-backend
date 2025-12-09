package com.dhanuka.indhan.SelfParticipationActivity.Model;

import androidx.annotation.NonNull;

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.io.Serializable;
import java.util.Date;

/**
 * Created by John on 6/29/2018.
 */
public class AtpList implements Serializable, Comparable<AtpList> {

    Date date;
    @SerializedName("plan_atp_id")
    @Expose
    private Integer planAtpId;
    @SerializedName("name")
    @Expose
    private String name;
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
    @SerializedName("village")
    @Expose
    private String village;
    @SerializedName("product_1")
    @Expose
    private String product1;
    @SerializedName("crop_1")
    @Expose
    private String crop1;
    @SerializedName("plan_atp_status")
    @Expose
    private Integer planAtpStatus;
    private int valid_status;

    public Date getDate() {
        return date;
    }

    public void setDate(Date date) {
        this.date = date;
    }

    public int getValid_status() {
        return valid_status;
    }

    public void setValid_status(int valid_status) {
        this.valid_status = valid_status;
    }

    public Integer getPlanAtpId() {
        return planAtpId;
    }

    public void setPlanAtpId(Integer planAtpId) {
        this.planAtpId = planAtpId;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
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

    public String getVillage() {
        return village;
    }

    public void setVillage(String village) {
        this.village = village;
    }

    public String getProduct1() {
        return product1;
    }

    public void setProduct1(String product1) {
        this.product1 = product1;
    }

    public String getCrop1() {
        return crop1;
    }

    public void setCrop1(String crop1) {
        this.crop1 = crop1;
    }

    public Integer getPlanAtpStatus() {
        return planAtpStatus;
    }

    public void setPlanAtpStatus(Integer planAtpStatus) {
        this.planAtpStatus = planAtpStatus;
    }

    @Override
    public int compareTo(@NonNull AtpList atpList) {
        return getDate().compareTo(atpList.getDate());
    }
}
