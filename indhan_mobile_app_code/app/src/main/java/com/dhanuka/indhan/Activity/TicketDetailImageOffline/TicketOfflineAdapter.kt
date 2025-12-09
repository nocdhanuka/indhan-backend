package com.dhanuka.indhan.Activity.TicketDetailImageOffline


import android.graphics.BitmapFactory
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.DataBase.TableDataModel.ImageOfflineModel
import com.dhanuka.indhan.R
import kotlinx.android.synthetic.main.layout_ticket_offline.view.*
import java.io.File
import java.util.*


/**
 * Created by Shivam on 3/6/2018.
 */

class TicketOfflineAdapter(
    imageOfflineModel: ArrayList<ImageOfflineModel>,
    private val mActivityContext: AppCompatActivity,
    var clickListener: OnClickListener
) : RecyclerView.Adapter<TicketOfflineAdapter.MyViewHolder>() {

    private val filteredData: List<ImageOfflineModel>

    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    init {
        this.filteredData = imageOfflineModel
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): TicketOfflineAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_ticket_offline, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: TicketOfflineAdapter.MyViewHolder, position: Int) {
        val imageOfflineModel = filteredData[position]
        if (imageOfflineModel.isUploaded != 1) {
            val imgfile = File(imageOfflineModel.image_url_local)
            val bitmap = BitmapFactory.decodeFile(imgfile.toString())
            holder.itemView.imgOffline.setImageBitmap(bitmap)
            holder.itemView.datetv.text = imageOfflineModel.date
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }
}
