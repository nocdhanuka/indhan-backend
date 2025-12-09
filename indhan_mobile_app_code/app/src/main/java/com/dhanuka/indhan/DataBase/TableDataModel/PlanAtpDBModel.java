package com.dhanuka.indhan.DataBase.TableDataModel;


import androidx.annotation.NonNull;

import java.io.Serializable;
import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * Created by John on 5/23/20;18.
 */
public class PlanAtpDBModel implements Comparable<PlanAtpDBModel>, Serializable {
    int id = 0;
    int product_id = 0;
    int data_source_id = 0;
    int crop_id = 0;
    int district_id = 0;
    int state_id = 0;
    int tehsil_id = 0;
    int expected_farmer = 0;
    int isUploaded = 0;
    int languageId = 0;
    int expected_pda = 0;
    int territory_id = 0;
    String territory = "";
    String state = "";
    String district = "";
    String product = "";
    String crop = "";
    String crop_one = "";
    String crop_two = "";
    String data_source = "";
    String tehsil = "";
    String village = "";
    String record_id = "";
    String date = "";
    String language = "";
    String DD_ID = "";
    String IS_FL = "";
    String DATA_SOURCE = "";
    String presenter_id = "";
    String presenter_type = "";
    String monitor_id = "";
    String monitor_type = "";

    public int getTerritory_id() {
        return territory_id;
    }

    public void setTerritory_id(int territory_id) {
        this.territory_id = territory_id;
    }

    public String getTerritory() {
        return territory;
    }

    public void setTerritory(String territory) {
        this.territory = territory;
    }

    public String getPresenter_id() {
        return presenter_id;
    }

    public void setPresenter_id(String presenter_id) {
        this.presenter_id = presenter_id;
    }

    public String getPresenter_type() {
        return presenter_type;
    }

    public void setPresenter_type(String presenter_type) {
        this.presenter_type = presenter_type;
    }

    public String getMonitor_id() {
        return monitor_id;
    }

    public void setMonitor_id(String monitor_id) {
        this.monitor_id = monitor_id;
    }

    public String getMonitor_type() {
        return monitor_type;
    }

    public void setMonitor_type(String monitor_type) {
        this.monitor_type = monitor_type;
    }

    private Date date_obj;

    public PlanAtpDBModel() {
    }

    public PlanAtpDBModel(int id, int product_id, int crop_id, int data_source_id, int state_id,
                          int district_id, int tehsil_id, int expected_farmer, int isUploaded,
                          int expected_pda, int languageId, String village, String record_id,
                          String date, String crop_one, String crop_two, String language,
                          String PRODUCT_NAME, String CROP_NAME, String TEHSIL_NAME,
                          String DISTRICT_NAME, String STATE_NAME, String DATA_SOURCE,
                          String DD_ID, String isFL,String presenter_id, String presenter_type,
                          String monitor_id, String monitor_type,String territory,int territory_id) {
        this.id = id;
        this.territory = territory;
        this.territory_id = territory_id;
        this.product_id = product_id;
        this.crop_id = crop_id;
        this.data_source_id = data_source_id;
        this.state_id = state_id;
        this.district_id = district_id;
        this.tehsil_id = tehsil_id;
        this.expected_farmer = expected_farmer;
        this.isUploaded = isUploaded;
        this.expected_pda = expected_pda;
        this.village = village;
        this.record_id = record_id;
        this.date = date;
        this.crop_one = crop_one;
        this.crop_two = crop_two;
        this.language = language;
        this.languageId = languageId;
        this.product = PRODUCT_NAME;
        this.crop = CROP_NAME;
        this.district = DISTRICT_NAME;
        this.state = STATE_NAME;
        this.tehsil = TEHSIL_NAME;
        this.DD_ID = DD_ID;
        this.IS_FL = isFL;
        this.DATA_SOURCE = DATA_SOURCE;

        this.presenter_id = presenter_id;
        this.presenter_type = presenter_type;
        this.monitor_id = monitor_id;
        this.monitor_type = monitor_type;

        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd");
        try {
            date_obj = sdf.parse(date);
        } catch (Exception e) {
            e.printStackTrace();
        }
    }

    public int getLanguageId() {
        return languageId;
    }

    public void setLanguageId(int languageId) {
        this.languageId = languageId;
    }

    public String getLanguage() {
        return language;
    }

    public void setLanguage(String language) {
        this.language = language;
    }

    public String getCrop_one() {
        return crop_one;
    }

    public void setCrop_one(String crop_one) {
        this.crop_one = crop_one;
    }

    public String getCrop_two() {
        return crop_two;
    }

    public void setCrop_two(String crop_two) {
        this.crop_two = crop_two;
    }

    public Date getDate_obj() {
        return date_obj;
    }

    public void setDate_obj(Date date_obj) {
        this.date_obj = date_obj;
    }

    public String getDD_ID() {
        return DD_ID;
    }

    public void setDD_ID(String DD_ID) {
        this.DD_ID = DD_ID;
    }

    public String getIS_FL() {
        return IS_FL;
    }

    public void setIS_FL(String IS_FL) {
        this.IS_FL = IS_FL;
    }

    public String getDATA_SOURCE() {
        return DATA_SOURCE;
    }

    public void setDATA_SOURCE(String DATA_SOURCE) {
        this.DATA_SOURCE = DATA_SOURCE;
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

    public String getProduct() {
        return product;
    }

    public void setProduct(String product) {
        this.product = product;
    }

    public String getCrop() {
        return crop;
    }

    public void setCrop(String crop) {
        this.crop = crop;
    }

    public String getData_source() {
        return data_source;
    }

    public void setData_source(String data_source) {
        this.data_source = data_source;
    }

    public String getTehsil() {
        return tehsil;
    }

    public void setTehsil(String tehsil) {
        this.tehsil = tehsil;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getProduct_id() {
        return product_id;
    }

    public void setProduct_id(int product_id) {
        this.product_id = product_id;
    }

    public int getCrop_id() {
        return crop_id;
    }

    public void setCrop_id(int crop_id) {
        this.crop_id = crop_id;
    }

    public int getData_source_id() {
        return data_source_id;
    }

    public void setData_source_id(int data_source_id) {
        this.data_source_id = data_source_id;
    }

    public int getState_id() {
        return state_id;
    }

    public void setState_id(int state_id) {
        this.state_id = state_id;
    }

    public int getDistrict_id() {
        return district_id;
    }

    public void setDistrict_id(int district_id) {
        this.district_id = district_id;
    }

    public int getTehsil_id() {
        return tehsil_id;
    }

    public void setTehsil_id(int tehsil_id) {
        this.tehsil_id = tehsil_id;
    }

    public int getExpected_farmer() {
        return expected_farmer;
    }

    public void setExpected_farmer(int expected_farmer) {
        this.expected_farmer = expected_farmer;
    }

    public int getIsUploaded() {
        return isUploaded;
    }

    public void setIsUploaded(int isUploaded) {
        this.isUploaded = isUploaded;
    }

    public int getExpected_pda() {
        return expected_pda;
    }

    public void setExpected_pda(int expected_pda) {
        this.expected_pda = expected_pda;
    }

    public String getVillage() {
        return village;
    }

    public void setVillage(String village) {
        this.village = village;
    }

    public String getRecord_id() {
        return record_id;
    }

    public void setRecord_id(String record_id) {
        this.record_id = record_id;
    }

    public String getDate() {
        return date;
    }

    public void setDate(String date) {
        this.date = date;
    }

    @Override
    public int compareTo(@NonNull PlanAtpDBModel planAtpDBModel) {
        return getDate_obj().compareTo(planAtpDBModel.getDate_obj());
    }
}
