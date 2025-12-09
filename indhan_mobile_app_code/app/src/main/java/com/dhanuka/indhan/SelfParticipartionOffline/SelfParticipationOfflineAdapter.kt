package com.dhanuka.indhan.SelfParticipartionOffline


import android.content.Intent
import android.graphics.Color
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.DataBase.TableDataModel.ArrivalDepartureModelOffline
import com.dhanuka.indhan.R
import com.himangi.imagepreview.ImagePreviewActivity
import kotlinx.android.synthetic.main.layout_self_particiaption.view.*
import java.io.File
import kotlin.math.roundToInt

class SelfParticipationOfflineAdapter(
    selfParticipationMOffline: ArrayList<ArrivalDepartureModelOffline>,
    private val mActivityContext: AppCompatActivity,
    var clickListener: OnClickListener
) : RecyclerView.Adapter<SelfParticipationOfflineAdapter.MyViewHolder>() {

    private val filteredData: List<ArrivalDepartureModelOffline>


    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    init {
        this.filteredData = selfParticipationMOffline
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): SelfParticipationOfflineAdapter.MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_self_particiaption, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(
        holder: SelfParticipationOfflineAdapter.MyViewHolder,
        position: Int
    ) {
        val selfParticipationMOffline = filteredData[position]
        holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#ffffff"))
        holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#FF0CA160"))
        holder.itemView.tvMeTicketNo.text = selfParticipationMOffline.ticket_no

        holder.itemView.tvMeDate.text = selfParticipationMOffline.arrival_time
        holder.itemView.tvMeDescription.text = selfParticipationMOffline.comment
        if (selfParticipationMOffline.imageURL != null) {
            Glide.with(mActivityContext).load(File(selfParticipationMOffline.imageURL))
                .into(holder.itemView.ivArrivalImage)
        }

        holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FF7C7474"))
        holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#FF837B7B"))
        if (selfParticipationMOffline.departure_time != null) {
            holder.itemView.tvDepartureTime.text = selfParticipationMOffline.departure_time
        }
        if (selfParticipationMOffline.departure_comment != null) {
            holder.itemView.tvDepartureDescription.text =
                selfParticipationMOffline.departure_comment
        }
        if (selfParticipationMOffline.departure_imageURL != null) {
            Glide.with(mActivityContext).load(File(selfParticipationMOffline.departure_imageURL))
                .into(holder.itemView.ivDepartureImage)
        }

        holder.itemView.tvDepartureTime.setTextColor(Color.parseColor("#FF7C7474"))
        holder.itemView.tvDepartureDescription.setTextColor(Color.parseColor("#FF837B7B"))
        if (selfParticipationMOffline.duration != null) {
            if (selfParticipationMOffline.duration != null) {
                var duration = ""
                duration = if (selfParticipationMOffline.duration.contains(".")) {
                    "Activity Duration - ${selfParticipationMOffline.duration.split(".")[0].toInt()} hours " +
                            "0.${selfParticipationMOffline.duration.split(".")[1]}".toDouble()
                                .times(60)
                                .toInt() + " minutes"
                } else {
                    "Activity Duration - ${
                        (selfParticipationMOffline.duration.toDouble().roundToInt())
                    } hours"
                }
                holder.itemView.tvActivityDuration.text = duration
            }
        }

        holder.itemView.ivArrivalImage.setOnClickListener {
            val intent = Intent(mActivityContext, ImagePreviewActivity::class.java)
            if (selfParticipationMOffline.imageURL.length > 5) {
                val imageList: ArrayList<String> = ArrayList()
                imageList.add(selfParticipationMOffline.imageURL)
                intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList)
                intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0)
                mActivityContext.startActivity(intent)
            }
        }

        holder.itemView.ivDepartureImage.setOnClickListener {
            val intent = Intent(mActivityContext, ImagePreviewActivity::class.java)
            if (selfParticipationMOffline.departure_imageURL != null) {
                if (selfParticipationMOffline.departure_imageURL.length > 5) {
                    val imageList: ArrayList<String> = ArrayList()
                    imageList.add(selfParticipationMOffline.imageURL)
                    intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList)
                    intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0)
                    mActivityContext.startActivity(intent)
                }
            }
        }
    }

    override fun getItemCount(): Int {
        return filteredData.size
    }
}
