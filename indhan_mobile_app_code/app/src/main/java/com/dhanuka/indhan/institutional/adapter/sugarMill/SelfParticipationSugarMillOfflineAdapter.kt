package com.dhanuka.indhan.institutional.adapter.sugarMill


import android.content.Intent
import android.graphics.Color
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.RecyclerView
import com.bumptech.glide.Glide
import com.dhanuka.indhan.ATPMenu.activity.OnClickListener
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.sugarMill.SugarMillMarkArrivalDepartureAtpModel
import com.himangi.imagepreview.ImagePreviewActivity
import kotlinx.android.synthetic.main.layout_self_particiaption.view.*
import java.io.File
import java.util.*
import kotlin.math.roundToInt

class SelfParticipationSugarMillOfflineAdapter(
    selfParticipationMOffline: ArrayList<SugarMillMarkArrivalDepartureAtpModel>,
    private val mActivityContext: AppCompatActivity,
    var clickListener: OnClickListener
) : RecyclerView.Adapter<SelfParticipationSugarMillOfflineAdapter.MyViewHolder>() {

    private val filteredData: List<SugarMillMarkArrivalDepartureAtpModel>


    inner class MyViewHolder(view: View) : RecyclerView.ViewHolder(view) {

    }

    init {
        this.filteredData = selfParticipationMOffline
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): MyViewHolder {
        val itemView = LayoutInflater.from(parent.context)
            .inflate(R.layout.layout_self_particiaption, parent, false)
        return MyViewHolder(itemView)
    }

    override fun onBindViewHolder(
        holder: MyViewHolder,
        position: Int
    ) {
        val selfParticipationMOffline = filteredData[position]
        holder.itemView.rlBackground.setBackgroundColor(Color.parseColor("#ffffff"))
        holder.itemView.tvMeTicketNo.setTextColor(Color.parseColor("#FF0CA160"))
        holder.itemView.tvMeTicketNo.text = selfParticipationMOffline.ticket_number

        holder.itemView.tvMeDate.text = selfParticipationMOffline.arrival_date
        holder.itemView.tvMeDescription.text = selfParticipationMOffline.arrival_comment
        if (selfParticipationMOffline.arrival_image != null)
            Glide.with(mActivityContext).load(File(selfParticipationMOffline.arrival_image))
                .into(holder.itemView.ivArrivalImage)


        holder.itemView.tvMeDate.setTextColor(Color.parseColor("#FF7C7474"))
        holder.itemView.tvMeDescription.setTextColor(Color.parseColor("#FF837B7B"))
        if (selfParticipationMOffline.departure_date != null) {
            holder.itemView.tvDepartureTime.text = selfParticipationMOffline.departure_date
        }
        if (selfParticipationMOffline.departure_comment != null) {
            holder.itemView.tvDepartureDescription.text =
                selfParticipationMOffline.departure_comment
        }

        if (selfParticipationMOffline.departure_image != null)
            Glide.with(mActivityContext).load(File(selfParticipationMOffline.departure_image))
                .into(holder.itemView.ivDepartureImage)
        holder.itemView.tvDepartureTime.setTextColor(Color.parseColor("#FF7C7474"))
        holder.itemView.tvDepartureDescription.setTextColor(Color.parseColor("#FF837B7B"))
        if (!TextUtils.isEmpty(selfParticipationMOffline.duration)) {
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

        holder.itemView.ivArrivalImage.setOnClickListener {
            val intent = Intent(mActivityContext, ImagePreviewActivity::class.java)
            if ((selfParticipationMOffline.arrival_image?.length ?: 0) > 5) {
                val imageList: ArrayList<String> = ArrayList()
                imageList.add(selfParticipationMOffline.arrival_image ?: "")
                intent.putExtra(ImagePreviewActivity.IMAGE_LIST, imageList)
                intent.putExtra(ImagePreviewActivity.CURRENT_ITEM, 0)
                mActivityContext.startActivity(intent)
            }
        }

        holder.itemView.ivDepartureImage.setOnClickListener {
            val intent = Intent(mActivityContext, ImagePreviewActivity::class.java)
            if (selfParticipationMOffline.departure_image != null) {
                if ((selfParticipationMOffline.departure_image?.length ?: 0) > 5) {
                    val imageList: ArrayList<String> = ArrayList()
                    imageList.add(selfParticipationMOffline.departure_image ?: "")
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
