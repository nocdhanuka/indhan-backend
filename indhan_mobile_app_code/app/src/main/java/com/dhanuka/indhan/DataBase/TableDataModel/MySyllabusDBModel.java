package com.dhanuka.indhan.DataBase.TableDataModel;

import android.os.Build;
import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;

import java.io.Serializable;

/**
 * Created by John on 5/23/2018.
 */
public class MySyllabusDBModel implements Serializable, Comparable<MySyllabusDBModel> {

    int id;
    int product_id;
    String product_name;
    int crop_id;
    String crop_name;
    int no_of_days;
    int no_of_dd;
    int no_of_remainingDays;
    int month;
    int year;
    int atp;

    public int getYear() {
        return year;
    }

    public void setYear(int year) {
        this.year = year;
    }

    public int getMonth() {
        return month;
    }

    public void setMonth(int month) {
        this.month = month;
    }

    public int getNo_of_remainingDays() {
        return no_of_remainingDays;
    }

    public void setNo_of_remainingDays(int no_of_remainingDays) {
        this.no_of_remainingDays = no_of_remainingDays;
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

    public String getCrop_name() {
        return crop_name;
    }

    public void setCrop_name(String crop_name) {
        this.crop_name = crop_name;
    }

    public int getNo_of_days() {
        return no_of_days;
    }

    public void setNo_of_days(int no_of_days) {
        this.no_of_days = no_of_days;
    }

    public int getNo_of_dd() {
        return no_of_dd;
    }

    public void setNo_of_dd(int no_of_dd) {
        this.no_of_dd = no_of_dd;
    }

    public int getAtp() {
        return atp;
    }

    public void setAtp(int atp) {
        this.atp = atp;
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public int compareTo(@NonNull MySyllabusDBModel mySyllabusDBModel) {
        if (getYear() == mySyllabusDBModel.getYear()) {
            return Integer.compare(getMonth(), mySyllabusDBModel.getMonth());
        } else {
            return Integer.compare(getYear(), mySyllabusDBModel.getYear());
        }
    }
}