package noman.weekcalendar.eventbus;

import android.view.View;
import android.widget.TextView;

import org.joda.time.DateTime;


import java.util.ArrayList;

/**
 * Created by nor on 12/5/2015.
 */
public class Event {
    public static class OnDateClickEvent {
        public OnDateClickEvent(DateTime dateTime) {
            this.dateTime = dateTime;
        }

        private DateTime dateTime;

        public DateTime getDateTime() {
            return dateTime;
        }
    }

    public static class InvalidateEvent {
    }

    public static class UpdateSelectedDateEvent {
        /***
         * Direction -1 for backgroun and 1 for forward
         *
         * @param direction
         */
        public UpdateSelectedDateEvent(int direction) {
            this.direction = direction;
        }

        public int getDirection() {
            return direction;
        }

        private int direction;
    }

    public static class SetCurrentPageEvent {
        public int getDirection() {
            return direction;
        }

        public SetCurrentPageEvent(int direction) {

            this.direction = direction;
        }

        private int direction;
    }

    public static class ResetEvent {
    }

    public static class SetSelectedDateEvent {
        public SetSelectedDateEvent(DateTime selectedDate) {
            this.selectedDate = selectedDate;
        }

        public DateTime getSelectedDate() {
            return selectedDate;
        }

        private DateTime selectedDate;
    }

    /*public static class SetSelectedDateListEvent {
        public SetSelectedDateListEvent(ArrayList<DateTime> selectedlistDate) {
            this.selectedlistDate = selectedlistDate;
        }

        public ArrayList<DateTime> getSelectedDateList() {
            return selectedlistDate;
        }

        private ArrayList<DateTime> selectedlistDate;
    }*/


    public static class SetStartDateEvent {
        private DateTime startDate;
        private ArrayList<DateTime> selectedlistDate;

        public SetStartDateEvent(DateTime startDate,ArrayList<DateTime> selectedlistDate) {
            this.startDate = startDate;
            this.selectedlistDate = selectedlistDate;
        }

        public ArrayList<DateTime> getSelectedDateList() {
            return selectedlistDate;
        }
        public DateTime getStartDate() {
            return startDate;
        }
    }

    public static class OnDayDecorateEvent {

        private final View view;
        private final TextView dayTextView;
        private final DateTime dateTime;
        private DateTime firstDay;
        private DateTime selectedDateTime;
        private boolean isHigh;

        public OnDayDecorateEvent(View view, TextView dayTextView, DateTime dateTime, DateTime firstDayOfTheWeek, DateTime selectedDateTime,boolean isHigh) {
            this.view = view;
            this.dayTextView = dayTextView;
            this.dateTime = dateTime;
            this.firstDay = firstDayOfTheWeek;
            this.selectedDateTime = selectedDateTime;
            this.isHigh = isHigh;
        }

        public boolean isHigh() {
            return isHigh;
        }

        public View getView() {
            return view;
        }

        public TextView getDayTextView() {
            return dayTextView;
        }

        public DateTime getDateTime() {
            return dateTime;
        }

        public DateTime getFirstDay() {
            return firstDay;
        }

        public DateTime getSelectedDateTime() {
            return selectedDateTime;
        }
    }

    public static class OnWeekChange {

        private final DateTime firstDayOfTheWeek;
        private final boolean forward;

        public OnWeekChange(DateTime firstDayOfTheWeek, boolean isForward) {
            this.firstDayOfTheWeek = firstDayOfTheWeek;
            this.forward = isForward;
        }

        public DateTime getFirstDayOfTheWeek() {
            return firstDayOfTheWeek;
        }

        public boolean isForward() {
            return forward;
        }
    }

    public static class OnUpdateUi {
    }
}
