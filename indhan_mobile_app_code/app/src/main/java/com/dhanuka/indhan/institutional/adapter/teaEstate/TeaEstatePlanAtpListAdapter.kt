package com.dhanuka.indhan.institutional.adapter.teaEstate

import android.content.Context
import android.content.res.ColorStateList
import android.graphics.Color
import android.os.Build
import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.Preference.Preference
import com.dhanuka.indhan.R
import com.dhanuka.indhan.institutional.model.teaEstate.AtpListData
import kotlinx.android.synthetic.main.item_tea_estate_plan_atp.view.*
import java.text.SimpleDateFormat
import java.util.*

class TeaEstatePlanAtpListAdapter(
    var context: Context,
    var planAtpList: ArrayList<AtpListData>
) : RecyclerView.Adapter<TeaEstatePlanAtpListAdapter.ViewHolder>() {

    private lateinit var clickListener: OnClickItem
    private var preference: Preference? = null
    fun setClickListener(itemClickListener: OnClickItem) {
        this.clickListener = itemClickListener
    }

    inner class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        init {
            preference = Preference(context)
        }
    }

    override fun onCreateViewHolder(
        parent: ViewGroup,
        viewType: Int
    ): ViewHolder {
        val inflater =
            LayoutInflater.from(parent.context)
                .inflate(R.layout.item_tea_estate_plan_atp, parent, false)
        return ViewHolder(inflater)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        when (planAtpList[holder.absoluteAdapterPosition].typeOfData) {
            "planned" -> {
                holder.itemView.chbExpiredDisabled.visibility = View.VISIBLE
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                    holder.itemView.chbExpiredDisabled.buttonTintList =
                        ColorStateList.valueOf(0xFFff8921.toInt())
                } else {
                    holder.itemView.chbExpiredDisabled.setBackgroundColor(0xFFff8921.toInt())
                }
            }
            "visited" -> {
                holder.itemView.chbExpiredDisabled.visibility = View.VISIBLE
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                    holder.itemView.chbExpiredDisabled.buttonTintList =
                        ColorStateList.valueOf(0xFF34b1d5.toInt())
                } else {
                    holder.itemView.chbExpiredDisabled.setBackgroundColor(0xFF34b1d5.toInt())
                }
            }
            "expired" -> {
                holder.itemView.chbExpiredDisabled.visibility = View.VISIBLE
                if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.LOLLIPOP) {
                    holder.itemView.chbExpiredDisabled.buttonTintList =
                        ColorStateList.valueOf(0xFFff3f3f.toInt())
                } else {
                    holder.itemView.chbExpiredDisabled.setBackgroundColor(0xFFff3f3f.toInt())
                }
            }
            else -> {
                holder.itemView.chbExpiredDisabled.visibility = View.GONE
            }
        }
        if (planAtpList[position].is_arrival_departure == 2) {
            holder.itemView.txtDeparture.visibility = View.GONE
            holder.itemView.txtArrival.visibility = View.GONE
            if (!TextUtils.isEmpty(planAtpList[holder.absoluteAdapterPosition].arrival_image) && !TextUtils.isEmpty(
                    planAtpList[holder.absoluteAdapterPosition].departure_image
                )
            ) {
                holder.itemView.ivArrow.visibility = View.VISIBLE
            } else {
                holder.itemView.ivArrow.visibility = View.GONE
            }
        } else {
            holder.itemView.ivArrow.visibility = View.GONE
            holder.itemView.txtDeparture.visibility = View.VISIBLE
            holder.itemView.txtArrival.visibility = View.VISIBLE
            if (planAtpList[position].is_arrival_departure == 0) {
                holder.itemView.txtArrival.isEnabled = true
                holder.itemView.txtDeparture.isEnabled = false
                holder.itemView.txtArrival.setTextColor(Color.parseColor("#FFFFFFFF"))
                holder.itemView.txtArrival.setBackgroundColor(Color.parseColor("#FF1AAF56"))
                holder.itemView.txtDeparture.setBackgroundColor(Color.parseColor("#FF9B9B9B"))
                holder.itemView.txtDeparture.setTextColor(Color.parseColor("#FFFFFFFF"))
            } else {
                holder.itemView.txtArrival.isEnabled = false
                holder.itemView.txtDeparture.isEnabled = true
                holder.itemView.txtArrival.setBackgroundColor(Color.parseColor("#FF9B9B9B"))
                holder.itemView.txtDeparture.setBackgroundColor(Color.parseColor("#FF1AAF56"))
                holder.itemView.txtArrival.setTextColor(Color.parseColor("#FFFFFFFF"))
                holder.itemView.txtDeparture.setTextColor(Color.parseColor("#FFFFFFFF"))
            }
        }

        val type =
            if (planAtpList[holder.absoluteAdapterPosition].tea_estate_office_type == "1") "Head Office" else "Branch Office"
        holder.itemView.tvEstateData.text =
            "${planAtpList[holder.absoluteAdapterPosition].tea_estate_name} | $type"

        holder.itemView.tvMeTicketNo.text =
            planAtpList[holder.absoluteAdapterPosition].ticket_number

        var sdf = SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss.SSSZZZ", Locale.ENGLISH)
        val strDate: Date = sdf.parse(planAtpList[holder.absoluteAdapterPosition].date_time)
        sdf = SimpleDateFormat("dd, MMM yyyy hh:mm a", Locale.ENGLISH)
        val date = sdf.format(strDate)
        holder.itemView.tvMeDate.text = date

        holder.itemView.tvMeDescription.text =
            planAtpList[holder.absoluteAdapterPosition].state.plus(" | ")
                .plus(planAtpList[holder.absoluteAdapterPosition].district)
        holder.itemView.total_farmer.text = "Planned By - ".plus(planAtpList[holder.absoluteAdapterPosition].manager_name)
        holder.itemView.total_valid_farmers.text =
            "Category :- ".plus(planAtpList[holder.absoluteAdapterPosition].company_category)
        holder.itemView.rlBackground.setOnClickListener {
            if (!TextUtils.isEmpty(planAtpList[holder.absoluteAdapterPosition].arrival_image) && !TextUtils.isEmpty(
                    planAtpList[holder.absoluteAdapterPosition].departure_image
                )
            ) {
                clickListener.onClickView(it, planAtpList[position])
            }
        }

        holder.itemView.txtArrival.setOnClickListener {
            clickListener.onArrivalClickView(holder.absoluteAdapterPosition, planAtpList[position])
        }

        holder.itemView.txtDeparture.setOnClickListener {
            clickListener.onDepartureClickView(
                holder.absoluteAdapterPosition,
                planAtpList[position]
            )
        }
    }

    override fun getItemCount(): Int = planAtpList.size

    interface OnClickItem {
        fun onClickView(view: View, subMenu: AtpListData)
        fun onArrivalClickView(position: Int, subMenu: AtpListData)
        fun onDepartureClickView(position: Int, subMenu: AtpListData)
    }

}