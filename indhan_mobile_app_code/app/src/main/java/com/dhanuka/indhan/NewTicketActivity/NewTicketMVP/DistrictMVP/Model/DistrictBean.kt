package com.dhanuka.indhan.Activity.NewTicketMVP.DistrictMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/5/2018.
 */

class DistrictBean {
    @SerializedName("dID")
    @Expose
    private var dID: Int? = null

    @SerializedName("Item")
    @Expose
    var item: String? = null

    fun getdID(): Int? {
        return dID
    }

    fun setdID(dID: Int?) {
        this.dID = dID
    }
}
