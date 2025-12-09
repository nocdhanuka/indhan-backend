package com.dhanuka.indhan.DataBase.TableDataModel;

/**
 * Created by John on 5/9/2018.
 */
public class CropModel {
    int id, pid;
    String cropName;

    public CropModel(int id, int pid, String cropName) {
        this.id = id;
        this.pid = pid;
        this.cropName = cropName;
    }

    public CropModel() {
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getPid() {
        return pid;
    }

    public void setPid(int pid) {
        this.pid = pid;
    }

    public String getCropName() {
        return cropName;
    }

    public void setCropName(String cropName) {
        this.cropName = cropName;
    }
}
