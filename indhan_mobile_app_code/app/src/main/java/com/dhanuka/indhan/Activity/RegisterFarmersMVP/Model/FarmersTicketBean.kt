package com.dhanuka.indhan.Activity.RegisterFarmersMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/8/2018.
 */

class FarmersTicketBean {

    @SerializedName("tID")
    @Expose
    private var tID: Int? = null

    @SerializedName("ticket_no")
    @Expose
    var ticket_no: String? = null

    @SerializedName("datetime_format")
    @Expose
    var datetime_format: String? = null

    @SerializedName("datetime")
    @Expose
    var datetime: String? = null

    fun gettID(): Int? {
        return tID
    }

    fun settID(tID: Int?) {
        this.tID = tID
    }
}
