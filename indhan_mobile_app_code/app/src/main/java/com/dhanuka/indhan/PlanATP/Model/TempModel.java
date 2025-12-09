package com.dhanuka.indhan.PlanATP.Model;

import android.os.Build;
import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;

/**
 * Created by John on 6/3/2018.
 */
public class TempModel implements Comparable<TempModel> {
    int prodId, cropId, no_of_days;
    String prod_name, crop_name;
    int month;
    int allready_uploaded;
    int syllabusDays;
    int isUploaded;

    public int getAllready_uploaded() {
        return allready_uploaded;
    }

    public void setAllready_uploaded(int allready_uploaded) {
        this.allready_uploaded = allready_uploaded;
    }

    public int getIsUploaded() {
        return isUploaded;
    }

    public void setIsUploaded(int isUploaded) {
        this.isUploaded = isUploaded;
    }

    public int getMonth() {
        return month;
    }

    public void setMonth(int month) {
        this.month = month;
    }

    public int getSyllabusDays() {
        return syllabusDays;
    }

    public void setSyllabusDays(int syllabusDays) {
        this.syllabusDays = syllabusDays;
    }

    public int getProdId() {
        return prodId;
    }

    public void setProdId(int prodId) {
        this.prodId = prodId;
    }

    public int getCropId() {
        return cropId;
    }

    public void setCropId(int cropId) {
        this.cropId = cropId;
    }

    public int getNo_of_days() {
        return no_of_days;
    }

    public void setNo_of_days(int no_of_days) {
        this.no_of_days = no_of_days;
    }

    public String getProd_name() {
        return prod_name;
    }

    public void setProd_name(String prod_name) {
        this.prod_name = prod_name;
    }

    public String getCrop_name() {
        return crop_name;
    }

    public void setCrop_name(String crop_name) {
        this.crop_name = crop_name;
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public int compareTo(@NonNull TempModel tempModel) {
        return Integer.compare(getMonth(), tempModel.getMonth());
    }
}
