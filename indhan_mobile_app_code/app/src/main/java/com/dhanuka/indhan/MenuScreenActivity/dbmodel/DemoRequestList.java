package com.dhanuka.indhan.MenuScreenActivity.dbmodel;

import androidx.room.Entity;
import androidx.room.PrimaryKey;

@Entity
public class DemoRequestList {

    @PrimaryKey(autoGenerate = true)
    private Integer _id;

    private String acres;

    private String contact_number;

    private String created_at;

    private String crop_id;

    private String dd_id;

    private String farmer_type;
    private int demo_request_id;

    private String demo_code;

    private String district;

    private String district_id;

    private String farmer_name;

    private String pincode;

    private String product;

    private String product_id;

    private String showing_date;

    private String spray;

    private String state;

    private String state_id;

    private String status;

    private String tehsil;

    private String tehsil_id;

    private String updated_at;

    private String village_id;

    private String village;

    private String demoImage;

    private String farmer_image;

    private String latitude;

    private String longitude;
    private boolean isSelected;

    private String crop;

    public Integer get_id() {
        return _id;
    }

    public void set_id(Integer _id) {
        this._id = _id;
    }

    public String getAcres() {
        return acres;
    }

    public void setAcres(String acres) {
        this.acres = acres;
    }

    public String getContact_number() {
        return contact_number;
    }

    public void setContact_number(String contact_number) {
        this.contact_number = contact_number;
    }

    public String getCreated_at() {
        return created_at;
    }

    public void setCreated_at(String created_at) {
        this.created_at = created_at;
    }

    public String getCrop_id() {
        return crop_id;
    }

    public void setCrop_id(String crop_id) {
        this.crop_id = crop_id;
    }

    public String getDd_id() {
        return dd_id;
    }

    public void setDd_id(String dd_id) {
        this.dd_id = dd_id;
    }

    public String getFarmer_type() {
        return farmer_type;
    }

    public void setFarmer_type(String farmer_type) {
        this.farmer_type = farmer_type;
    }

    public int getDemo_request_id() {
        return demo_request_id;
    }

    public void setDemo_request_id(int demo_request_id) {
        this.demo_request_id = demo_request_id;
    }

    public String getDemo_code() {
        return demo_code;
    }

    public void setDemo_code(String demo_code) {
        this.demo_code = demo_code;
    }

    public String getDistrict() {
        return district;
    }

    public void setDistrict(String district) {
        this.district = district;
    }

    public String getDistrict_id() {
        return district_id;
    }

    public void setDistrict_id(String district_id) {
        this.district_id = district_id;
    }

    public String getFarmer_name() {
        return farmer_name;
    }

    public void setFarmer_name(String farmer_name) {
        this.farmer_name = farmer_name;
    }

    public String getPincode() {
        return pincode;
    }

    public void setPincode(String pincode) {
        this.pincode = pincode;
    }

    public String getProduct() {
        return product;
    }

    public void setProduct(String product) {
        this.product = product;
    }

    public String getProduct_id() {
        return product_id;
    }

    public void setProduct_id(String product_id) {
        this.product_id = product_id;
    }

    public String getShowing_date() {
        return showing_date;
    }

    public void setShowing_date(String showing_date) {
        this.showing_date = showing_date;
    }

    public String getSpray() {
        return spray;
    }

    public void setSpray(String spray) {
        this.spray = spray;
    }

    public String getState() {
        return state;
    }

    public void setState(String state) {
        this.state = state;
    }

    public String getState_id() {
        return state_id;
    }

    public void setState_id(String state_id) {
        this.state_id = state_id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public String getTehsil() {
        return tehsil;
    }

    public void setTehsil(String tehsil) {
        this.tehsil = tehsil;
    }

    public String getTehsil_id() {
        return tehsil_id;
    }

    public void setTehsil_id(String tehsil_id) {
        this.tehsil_id = tehsil_id;
    }

    public String getUpdated_at() {
        return updated_at;
    }

    public void setUpdated_at(String updated_at) {
        this.updated_at = updated_at;
    }

    public String getVillage_id() {
        return village_id;
    }

    public void setVillage_id(String village_id) {
        this.village_id = village_id;
    }

    public String getVillage() {
        return village;
    }

    public void setVillage(String village) {
        this.village = village;
    }

    public String getDemoImage() {
        return demoImage;
    }

    public void setDemoImage(String demoImage) {
        this.demoImage = demoImage;
    }

    public String getFarmer_image() {
        return farmer_image;
    }

    public void setFarmer_image(String farmer_image) {
        this.farmer_image = farmer_image;
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

    public boolean isSelected() {
        return isSelected;
    }

    public void setSelected(boolean selected) {
        isSelected = selected;
    }

    public String getCrop() {
        return crop;
    }

    public void setCrop(String crop) {
        this.crop = crop;
    }
}
