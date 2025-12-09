package com.dhanuka.indhan.DataBase.TableDataModel;

/**
 * Created by John on 5/9/2018.
 */
public class TehsilDBModel {
    int id, did;
    String tehsilName;

    public TehsilDBModel() {
    }

    public TehsilDBModel(int id, int did, String tehsilName) {

        this.id = id;
        this.did = did;
        this.tehsilName = tehsilName;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }

    public int getDid() {
        return did;
    }

    public void setDid(int did) {
        this.did = did;
    }

    public String getTehsilName() {
        return tehsilName;
    }

    public void setTehsilName(String tehsilName) {
        this.tehsilName = tehsilName;
    }
}
