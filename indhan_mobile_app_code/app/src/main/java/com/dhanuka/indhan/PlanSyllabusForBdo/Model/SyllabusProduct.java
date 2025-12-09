package com.dhanuka.indhan.PlanSyllabusForBdo.Model;

import android.os.Build;
import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;

/**
 * Created by John on 5/7/2018.
 */
public class SyllabusProduct implements Comparable<SyllabusProduct> {

    int id;
    String crops_name;
    String product_name;
    String bdo_name;
    String timestamp;
    String month;
    int crop_id;
    int product_id;
    int no_of_dd;
    int no_of_days;
    int bdo_id;
    int isUploaded;
    String record_id;

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getIsUploaded() {
        return isUploaded;
    }

    public void setIsUploaded(int isUploaded) {
        this.isUploaded = isUploaded;
    }

    public String getRecord_id() {
        return record_id;
    }

    public void setRecord_id(String record_id) {
        this.record_id = record_id;
    }

    public String getTimestamp() {
        return timestamp;
    }

    public void setTimestamp(String timestamp) {
        this.timestamp = timestamp;
    }

    public String getMonth() {
        return month;
    }

    public void setMonth(String month) {
        this.month = month;
    }

    public String getBdo_name() {
        return bdo_name;
    }

    public void setBdo_name(String bdo_name) {
        this.bdo_name = bdo_name;
    }

    public int getBdo_id() {
        return bdo_id;
    }

    public void setBdo_id(int bdo_id) {
        this.bdo_id = bdo_id;
    }

    public String getCrops_name() {
        return crops_name;
    }

    public void setCrops_name(String crops_name) {
        this.crops_name = crops_name;
    }

    public String getProduct_name() {
        return product_name;
    }

    public void setProduct_name(String product_name) {
        this.product_name = product_name;
    }

    public int getCrop_id() {
        return crop_id;
    }

    public void setCrop_id(int crop_id) {
        this.crop_id = crop_id;
    }

    public int getProduct_id() {
        return product_id;
    }

    public void setProduct_id(int product_id) {
        this.product_id = product_id;
    }

    public int getNo_of_dd() {
        return no_of_dd;
    }

    public void setNo_of_dd(int no_of_dd) {
        this.no_of_dd = no_of_dd;
    }

    public int getNo_of_days() {
        return no_of_days;
    }

    public void setNo_of_days(int no_of_days) {
        this.no_of_days = no_of_days;
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public int compareTo(@NonNull SyllabusProduct syllabusProduct) {
        return Integer.compare(getId(), syllabusProduct.getId());
    }

}
