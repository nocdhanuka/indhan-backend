package com.dhanuka.indhan.DataBase.TableDataModel;

import android.os.Build;
import androidx.annotation.NonNull;
import androidx.annotation.RequiresApi;

/**
 * Created by John on 5/29/2018.
 */
public class PlanAtpList implements Comparable<PlanAtpList> {


    private Integer id;
    private Integer local_id;
    private String name;
    private Integer activites;
    private Integer month;

    public Integer getLocal_id() {
        return local_id;
    }

    public void setLocal_id(Integer local_id) {
        this.local_id = local_id;
    }

    public Integer getMonth() {
        return month;
    }

    public void setMonth(Integer month) {
        this.month = month;
    }

    public Integer getId() {
        return id;
    }

    public void setId(Integer id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public Integer getActivites() {
        return activites;
    }

    public void setActivites(Integer activites) {
        this.activites = activites;
    }

    @RequiresApi(api = Build.VERSION_CODES.KITKAT)
    @Override
    public int compareTo(@NonNull PlanAtpList planAtpList) {
        return Integer.compare(getMonth(), planAtpList.getMonth());
    }
}
