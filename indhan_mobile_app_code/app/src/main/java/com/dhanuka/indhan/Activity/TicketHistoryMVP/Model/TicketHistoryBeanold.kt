package com.dhanuka.indhan.Activity.TicketHistoryMVP.Model

import com.google.gson.annotations.Expose
import com.google.gson.annotations.SerializedName

/**
 * Created by Shivam on 3/6/2018.
 */

class TicketHistoryBeanold {

    @SerializedName("tID")
    @Expose
    private var tID: Int? = null

    @SerializedName("ticket")
    @Expose
    var ticket: String? = null

    @SerializedName("datetime_format")
    @Expose
    var datetime_format: String? = null

    @SerializedName("ticket_status")
    @Expose
    var ticket_status: String? = null

    @SerializedName("description")
    @Expose
    var description: String? = null

    @SerializedName("total_farmers")
    @Expose
    var total_farmers: String? = null

    @SerializedName("total_valid_farmers")
    @Expose
    var total_valid_farmers: String? = null

    fun gettID(): Int? {
        return tID
    }

    fun settID(tID: Int?) {
        this.tID = tID
    }
}
