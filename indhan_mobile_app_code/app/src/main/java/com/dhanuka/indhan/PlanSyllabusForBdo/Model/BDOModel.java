package com.dhanuka.indhan.PlanSyllabusForBdo.Model;

/**
 * Created by John on 5/7/2018.
 */
public class BDOModel {
    String name;
    int id;

    public BDOModel(String name, int id) {
        this.name = name;
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public int getId() {
        return id;
    }

    public void setId(int id) {
        this.id = id;
    }
}
