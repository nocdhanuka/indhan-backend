package com.dhanuka.indhan.DataBase.TableDataModel;

/**
 * Created by John on 5/5/2018.
 */
public class UserTeamModel {
    int ID;
    String USERNAME;
    String PROFILE_PIC;
    String EMAIL;
    String PHONE;
    String DESIGNATION_CODE;
    String DESIGNATION_NAME;
    int IS_ON_DEPUTATION;
    String PARENT_ID;

    public UserTeamModel(int ID, String USERNAME, String PROFILE_PIC, String EMAIL, String PHONE, String DESIGNATION_CODE, String DESIGNATION_NAME, int IS_ON_DEPUTATION, String PARENT_ID) {
        this.ID = ID;
        this.USERNAME = USERNAME;
        this.PROFILE_PIC = PROFILE_PIC;
        this.EMAIL = EMAIL;
        this.PHONE = PHONE;
        this.DESIGNATION_CODE = DESIGNATION_CODE;
        this.DESIGNATION_NAME = DESIGNATION_NAME;
        this.IS_ON_DEPUTATION = IS_ON_DEPUTATION;
        this.PARENT_ID = PARENT_ID;
    }

    public UserTeamModel() {
    }

    public int getID() {
        return ID;
    }

    public void setID(int ID) {
        this.ID = ID;
    }

    public String getUSERNAME() {
        return USERNAME;
    }

    public void setUSERNAME(String USERNAME) {
        this.USERNAME = USERNAME;
    }

    public String getPROFILE_PIC() {
        return PROFILE_PIC;
    }

    public void setPROFILE_PIC(String PROFILE_PIC) {
        this.PROFILE_PIC = PROFILE_PIC;
    }

    public String getEMAIL() {
        return EMAIL;
    }

    public void setEMAIL(String EMAIL) {
        this.EMAIL = EMAIL;
    }

    public String getPHONE() {
        return PHONE;
    }

    public void setPHONE(String PHONE) {
        this.PHONE = PHONE;
    }

    public String getDESIGNATION_CODE() {
        return DESIGNATION_CODE;
    }

    public void setDESIGNATION_CODE(String DESIGNATION_CODE) {
        this.DESIGNATION_CODE = DESIGNATION_CODE;
    }

    public String getDESIGNATION_NAME() {
        return DESIGNATION_NAME;
    }

    public void setDESIGNATION_NAME(String DESIGNATION_NAME) {
        this.DESIGNATION_NAME = DESIGNATION_NAME;
    }

    public int getIS_ON_DEPUTATION() {
        return IS_ON_DEPUTATION;
    }

    public void setIS_ON_DEPUTATION(int IS_ON_DEPUTATION) {
        this.IS_ON_DEPUTATION = IS_ON_DEPUTATION;
    }

    public String getPARENT_ID() {
        return PARENT_ID;
    }

    public void setPARENT_ID(String PARENT_ID) {
        this.PARENT_ID = PARENT_ID;
    }
}
