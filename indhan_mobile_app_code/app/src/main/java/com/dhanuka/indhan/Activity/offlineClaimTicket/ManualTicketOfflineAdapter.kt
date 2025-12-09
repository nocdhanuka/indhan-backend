package com.dhanuka.indhan.Activity.offlineClaimTicket


import android.graphics.BitmapFactory
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_ticket_offline.view.datetv
import kotlinx.android.synthetic.main.layout_ticket_offline.view.imgOffline
import kotlinx.android.synthetic.main.manual_ticket_claim.view.*
import java.io.File
import java.util.*


/**
 * Created by Shivam on 3/6/2018.
 */

class ManualTicketOfflineAdapter(
    imageOfflineModel: ArrayList<ManualTicketClaimImage>,
    var clickListener: OnClickListener
) : RecyclerView.Adapter<ManualTicketOfflineAdapter.MyViewHolder>() {

    private val filteredData: List<ManualTicketClaimImage>

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    init {
        this.filteredData = imageOfflineModel
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ManualTicketOfflineAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.manual_ticket_claim, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: ManualTicketOfflineAdapter.MyViewHolder, position: Int) {
        val imageOfflineModel = filteredData[position]
        if (imageOfflineModel.isUploaded != 1) {
            val imgfile = File(imageOfflineModel.image_url_local)
            val bitmap = BitmapFactory.decodeFile(imgfile.toString())
            holder.itemView.imgOffline.setImageBitmap(bitmap)
            holder.itemView.datetv.text = imageOfflineModel.date
            holder.itemView.ticketNumber.text = imageOfflineModel.ticket_number
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }
}
