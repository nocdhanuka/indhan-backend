package com.dhanuka.indhan.DataBase.TableDataModel;

/**
 * Created by John on 5/9/2018.
 */
public class DistrictDBModel {
    int id, stateId;
    String districtName;


    public DistrictDBModel() {
    }

    public DistrictDBModel(int id, int stateId, String districtName) {
        this.id = id;
        this.stateId = stateId;
        this.districtName = districtName;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getStateId() {
        return stateId;
    }

    public void setStateId(int stateId) {
        this.stateId = stateId;
    }

    public String getDistrictName() {
        return districtName;
    }

    public void setDistrictName(String districtName) {
        this.districtName = districtName;
    }
}
