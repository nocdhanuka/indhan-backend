package com.dhanuka.indhan.DemoRequestEvaluated.adapters

import android.text.TextUtils
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.RecyclerView
import com.dhanuka.indhan.DemoRequestEvaluated.activities.RequestMaterialDetailActivity
import com.dhanuka.indhan.DemoRequestEvaluated.models.TimeLineModel
import com.dhanuka.indhan.R
import com.dhanuka.indhan.util.VectorDrawableUtils
import kotlinx.android.synthetic.main.item_time_line_view.view.*
import kotlinx.android.synthetic.main.time_line_item_reeceiving.view.*
import kotlinx.android.synthetic.main.time_line_view_approval.view.*

class TimeLineViewAdapter(
    private val mFeedList: List<TimeLineModel>,
    private val ctx: RequestMaterialDetailActivity
) :
    RecyclerView.Adapter<RecyclerView.ViewHolder>() {

    private lateinit var mLayoutInflater: LayoutInflater
    private lateinit var clickListener: ReceiveMaterialClick

    override fun getItemViewType(position: Int): Int {
        return when {
            position == 0 -> 1
            mFeedList[position].data == "changeView" -> 2
            mFeedList[position].type.equals(
                "Approval Status",
                ignoreCase = false
            ) -> 3
            else -> 0
        }
    }


    fun setClickListener(itemClickListener: ReceiveMaterialClick) {
        this.clickListener = itemClickListener
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        if (!::mLayoutInflater.isInitialized) {
            mLayoutInflater = LayoutInflater.from(parent.context)
        }
        when (viewType) {
            2 -> {
                return TimeLineViewHolder2(
                    mLayoutInflater.inflate(
                        R.layout.time_line_item_reeceiving,
                        parent,
                        false
                    ), viewType
                )
            }
            3 -> {
                return TimeLineViewHolder3(
                    mLayoutInflater.inflate(
                        R.layout.time_line_view_approval,
                        parent,
                        false
                    ), viewType
                )
            }
            else -> {
                return TimeLineViewHolder(
                    mLayoutInflater.inflate(
                        R.layout.item_time_line_view,
                        parent,
                        false
                    ), viewType
                )
            }
        }
    }


    private fun setMarker(holder: RecyclerView.ViewHolder, drawableResId: Int, colorFilter: Int) {
        when (holder) {
            is TimeLineViewHolder -> {
                holder.itemView.timelineRegular.marker = VectorDrawableUtils.getDrawable(
                    holder.itemView.context,
                    drawableResId,
                    ContextCompat.getColor(holder.itemView.context, colorFilter)
                )
            }
            is TimeLineViewHolder3 -> {
                holder.itemView.timelineApproval.marker = VectorDrawableUtils.getDrawable(
                    holder.itemView.context,
                    drawableResId,
                    ContextCompat.getColor(holder.itemView.context, colorFilter)
                )
            }
            else -> {
                holder.itemView.timelineReceiving.marker = VectorDrawableUtils.getDrawable(
                    holder.itemView.context,
                    drawableResId,
                    ContextCompat.getColor(holder.itemView.context, colorFilter)
                )
            }
        }

    }

    override fun getItemCount() = mFeedList.size

    inner class TimeLineViewHolder(itemView: View, viewType: Int) :
        RecyclerView.ViewHolder(itemView) {

        init {
            itemView.timelineRegular.initLine(viewType)
        }
    }

    inner class TimeLineViewHolder2(itemView: View, viewType: Int) :
        RecyclerView.ViewHolder(itemView) {

        init {
            itemView.timelineReceiving.initLine(viewType)
        }
    }

    inner class TimeLineViewHolder3(itemView: View, viewType: Int) :
        RecyclerView.ViewHolder(itemView) {

        init {
            itemView.timelineApproval.initLine(viewType)
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        val timeLineModel = mFeedList[position]
        if (holder is TimeLineViewHolder) {
            if (timeLineModel.comingStatus == 0) {
                setMarker(holder, R.drawable.ic_marker_inactive, R.color.orange_demo)
            } else {
                setMarker(holder, R.drawable.ic_marker_active, R.color.progess_green)
            }
            when (timeLineModel.status) {
                0 -> {
                    holder.itemView.timelineRegular.setStartLineColor(R.color.transparent, 1)
                    when (timeLineModel.comingStatus) {
                        0 -> {
                            holder.itemView.timelineRegular.setEndLineColor(R.color.charcoalGrey, 0)
                        }
                        2 -> {
                            holder.itemView.timelineRegular.setEndLineColor(R.color.charcoalGrey, 0)
                        }
                        else -> {
                            holder.itemView.timelineRegular.setEndLineColor(R.color.green_btn, 0)
                        }
                    }
                }
                3 -> {
                    holder.itemView.timelineRegular.setEndLineColor(R.color.transparent, 2)
                    when (timeLineModel.comingStatus) {
                        0 -> {
                            holder.itemView.timelineRegular.setStartLineColor(
                                R.color.charcoalGrey,
                                0
                            )
                        }
                        2 -> {
                            holder.itemView.timelineRegular.setStartLineColor(
                                R.color.charcoalGrey,
                                0
                            )
                        }
                        else -> {
                            holder.itemView.timelineRegular.setStartLineColor(R.color.green_btn, 0)
                        }
                    }
                }
                else -> {
                    when (timeLineModel.comingStatus) {
                        0 -> {
                            holder.itemView.timelineRegular.setStartLineColor(
                                R.color.charcoalGrey,
                                0
                            )
                            holder.itemView.timelineRegular.setEndLineColor(R.color.charcoalGrey, 0)
                        }
                        2 -> {
                            holder.itemView.timelineRegular.setStartLineColor(
                                R.color.charcoalGrey,
                                0
                            )
                            holder.itemView.timelineRegular.setEndLineColor(R.color.charcoalGrey, 0)
                        }
                        else -> {
                            holder.itemView.timelineRegular.setStartLineColor(R.color.green_btn, 0)
                            holder.itemView.timelineRegular.setEndLineColor(R.color.green_btn, 0)
                        }
                    }
                }
            }
            if (timeLineModel.time?.isNotEmpty() == true) {
                holder.itemView.text_timeline_date.visibility = View.VISIBLE
                holder.itemView.text_timeline_date.text = timeLineModel.time
            } else
                holder.itemView.text_timeline_date.visibility = View.GONE

            holder.itemView.text_timeline_title.text = timeLineModel.data
            holder.itemView.text_timeline_value.text = timeLineModel.dataValue
            holder.itemView.text_timeline_head.text = timeLineModel.type
        } else if (holder is TimeLineViewHolder3) {
            if (timeLineModel.comingStatus == 0) {
                setMarker(holder, R.drawable.ic_marker_inactive, R.color.orange_demo)
            } else {
                setMarker(holder, R.drawable.ic_marker_active, R.color.progess_green)
            }
            if (timeLineModel.time?.isNotEmpty() == true) {
                holder.itemView.text_timeline_date_apr.visibility = View.VISIBLE
                holder.itemView.text_timeline_date_apr.text = timeLineModel.time
            } else {
                holder.itemView.text_timeline_date_apr.visibility = View.GONE
            }
            holder.itemView.text_timeline_head_apr.text = timeLineModel.type
            when (timeLineModel.comingStatus) {
                0 -> {
                    holder.itemView.timelineApproval.setStartLineColor(
                        R.color.charcoalGrey,
                        0
                    )
                    holder.itemView.timelineApproval.setEndLineColor(R.color.charcoalGrey, 0)
                }
                2 -> {
                    holder.itemView.timelineApproval.setStartLineColor(
                        R.color.charcoalGrey,
                        0
                    )
                    holder.itemView.timelineApproval.setEndLineColor(R.color.charcoalGrey, 0)
                }
                else -> {
                    holder.itemView.timelineApproval.setStartLineColor(R.color.green_btn, 0)
                    holder.itemView.timelineApproval.setEndLineColor(R.color.green_btn, 0)
                }
            }
            holder.itemView.etApproveUnit.setText(timeLineModel.approveQty)
            holder.itemView.btnApprove.setOnClickListener {
                if (!TextUtils.isEmpty(holder.itemView.etApproveUnit.text.toString().trim())) {
                    clickListener.onClickApproveRejectMaterial(
                        timeLineModel,
                        holder.itemView.etApproveUnit.text.toString().trim(), 1
                    )
                } else {
                    ctx.makeToast("Please enter received quantity.")
                }
            }
            holder.itemView.btnReject.setOnClickListener {
                if (!TextUtils.isEmpty(holder.itemView.etApproveUnit.text.toString().trim())) {
                    clickListener.onClickApproveRejectMaterial(
                        timeLineModel,
                        holder.itemView.etApproveUnit.text.toString().trim(), 2
                    )
                } else {
                    ctx.makeToast("Please enter received quantity.")
                }
            }
        } else {
            if (timeLineModel.comingStatus == 0) {
                setMarker(holder, R.drawable.ic_marker_inactive, R.color.orange_demo)
            } else {
                setMarker(holder, R.drawable.ic_marker_active, R.color.progess_green)
            }
            if (timeLineModel.time?.isNotEmpty() == true) {
                holder.itemView.text_timeline_date_rcv.visibility = View.VISIBLE
                holder.itemView.text_timeline_date_rcv.text = timeLineModel.time
            } else {
                holder.itemView.text_timeline_date_rcv.visibility = View.GONE
            }
            holder.itemView.text_timeline_head_rcv.text = timeLineModel.type
            holder.itemView.timelineReceiving.setEndLineColor(R.color.transparent, 2)
            if (timeLineModel.comingStatus == 0) {
                holder.itemView.timelineReceiving.setStartLineColor(R.color.charcoalGrey, 0)
            } else {
                holder.itemView.timelineReceiving.setStartLineColor(R.color.green_btn, 0)
            }
            if (!TextUtils.isEmpty(timeLineModel.selectedReceiveDate)) {
                holder.itemView.txtReceivedBy.text = timeLineModel.selectedReceiveDate
            }
            holder.itemView.etReceivedUnit.setText(timeLineModel.receivedQty)
            holder.itemView.txtReceivedBy.setOnClickListener {
                clickListener.onClickDate(
                    timeLineModel,
                    holder.itemView.txtReceivedBy,
                    holder.itemView.etReceivedUnit.text.toString(),
                    holder.absoluteAdapterPosition
                )
            }

            holder.itemView.btnSubmit.setOnClickListener {
                if (!TextUtils.isEmpty(holder.itemView.etReceivedUnit.text.toString().trim())) {
                    clickListener.onClickReceiveMaterial(
                        timeLineModel,
                        holder.itemView.etReceivedUnit.text.toString().trim()
                    )
                } else {
                    ctx.makeToast("Please enter received quantity.")
                }

            }

        }
    }

    interface ReceiveMaterialClick {
        fun onClickReceiveMaterial(data: TimeLineModel, trim: String)
        fun onClickApproveRejectMaterial(data: TimeLineModel, trim: String, type: Int)
        fun onClickDate(data: TimeLineModel, view: TextView, count: String, pos: Int)
    }

}